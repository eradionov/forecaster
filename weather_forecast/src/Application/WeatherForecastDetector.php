<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\DTO\CityWeatherForecast;
use App\Application\DTO\MusementCity;
use App\Application\Renderer\WeatherForecastRendererInterface;
use App\Application\Repository\ApiHandlerRepositoryInterface;
use App\Utils\RequestParams;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class WeatherForecastDetector
{
    private const REQUESTED_FORECAST_DAYS = 2;

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

    public function detect(): void
    {
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
                $cityForecast = $this->getCityForecast($city);

                if (!$cityForecast) {
                    continue;
                }

                $this->renderer->render($cityForecast);
            } catch (\Throwable $exception) {
                $hasErrors = true;
                $this->notifier->debug($exception->getMessage());
            }
        }

        if ($hasErrors) {
            $this->notifier->error('Some errors occurred during processing, please see log for details.');
        }
    }

    /**
     * @param MusementCity $city
     *
     * @return CityWeatherForecast|null
     */
    private function getCityForecast(MusementCity $city): ?CityWeatherForecast
    {
        $cityWeatherForecast = $this->musementApiRepository->getMusementCityForecaseApiHandler()->fetch(
            RequestParams::create([
                'days' => self::REQUESTED_FORECAST_DAYS,
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
