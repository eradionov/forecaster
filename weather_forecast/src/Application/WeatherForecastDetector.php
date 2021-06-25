<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\DTO\CityWeatherForecast;
use App\Application\DTO\MusementCity;
use App\Application\Exception\MusementCityProcessingException;
use App\Application\Renderer\WeatherForecastRendererInterface;
use App\Application\Repository\ApiHandlerRepositoryInterface;
use App\Exception\HttpResponseException;
use App\Utils\RequestParams;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class WeatherForecastDetector
{
    private ApiHandlerRepositoryInterface $musementApiRepository;
    private ValidatorInterface $validator;
    private LoggerInterface $notifier;
    private WeatherForecastRendererInterface $renderer;
    private string $key;

    /**
     * @param ApiHandlerRepositoryInterface $musementApiRepository
     * @param ValidatorInterface $validator
     * @param LoggerInterface $notifier
     * @param WeatherForecastRendererInterface $renderer
     * @param string $key
     */
    public function __construct(
        ApiHandlerRepositoryInterface $musementApiRepository,
        ValidatorInterface $validator,
        LoggerInterface $notifier,
        WeatherForecastRendererInterface $renderer,
        string $key
    ) {
        $this->musementApiRepository = $musementApiRepository;
        $this->validator = $validator;
        $this->key = $key;
        $this->notifier = $notifier;
        $this->renderer = $renderer;
    }

    /**
     * @param int $days
     *
     * @throws MusementCityProcessingException if errors were detected during validation, API call and processing.
     * @throws HttpResponseException if response code is not 200.
     * @throws TransportExceptionInterface When a network error occurs
     * @throws RedirectionExceptionInterface On a 3xx when $throw is true and the "max_redirects" option has been reached
     * @throws ClientExceptionInterface On a 4xx when $throw is true
     * @throws ServerExceptionInterface On a 5xx when $throw is true
     * @throws \InvalidArgumentException if number of days to forecast weather is <= 0.
     */
    public function detect(int $days): void
    {
        if ($days <= 0) {
            throw new \InvalidArgumentException('Number of days to forecast weather should be positive.');
        }

        $cities = $this->musementApiRepository->getMusementCityApiHandler()->fetch();
        $hasErrors = false;

        if (count($cities) === 0) {
            $this->notifier->info('There were no cities returned from \'Musement API\'');

            return;
        }

        foreach ($cities as $city) {
            /* @var ConstraintViolationList $errors */
            $errors = $this->validator->validate($city);

            if (count($errors) > 0) {
                $hasErrors = true;

                /* @phpstan-ignore-next-line */
                $this->notifier->debug((string) $errors);

                continue;
            }

            try {
                $cityForecast = $this->getCityForecast($city, $days);

                if ($cityForecast === null) {
                    $hasErrors = true;

                    continue;
                }

                $this->renderer->render($cityForecast);
            } catch (\Throwable $exception) {
                $hasErrors = true;
                $this->notifier->debug($exception->getMessage());
            }
        }

        if ($hasErrors) {
            throw new MusementCityProcessingException();
        }
    }

    /**
     * @param MusementCity $city
     * @param int $days
     *
     * @return CityWeatherForecast|null
     */
    private function getCityForecast(MusementCity $city, int $days): ?CityWeatherForecast
    {
        $cityWeatherForecast = $this->musementApiRepository->getMusementCityForecaseApiHandler()->fetch(
            RequestParams::create([
                'days' => $days,
                'q' => sprintf('%f,%f', $city->getLatitude(), $city->getLongitude()),
            ],
                [
                    'key' => $this->key,
                ]
            )
        );

        $errors = $this->validator->validate($cityWeatherForecast);

        if (count($errors) > 0) {
            /* @phpstan-ignore-next-line */
            $this->notifier->debug((string) $errors);

            return null;
        }

        return $cityWeatherForecast;
    }
}
