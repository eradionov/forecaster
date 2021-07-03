<?php

declare(strict_types=1);

namespace App\WeatherForecast;

use App\ApiClient\Interfaces\MusementApiInterface;
use App\ApiClient\Interfaces\WeatherApiInterface;
use App\DTO\CityWeatherForecast;
use App\DTO\MusementCity;
use App\Exception\HttpResponseException;
use App\Exception\MusementCityProcessingException;
use App\Renderer\WeatherForecastRendererInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class WeatherForecastDetector
{
    private MusementApiInterface $musementCityApiClient;
    private WeatherApiInterface $weatherApiClient;
    private ValidatorInterface $validator;
    private LoggerInterface $consoleNotifier;
    private WeatherForecastRendererInterface $renderer;

    /**
     * @param MusementApiInterface $musementCityApiClient
     * @param WeatherApiInterface $weatherApiClient
     * @param ValidatorInterface $validator
     * @param LoggerInterface $consoleNotifier
     * @param WeatherForecastRendererInterface $renderer
     */
    public function __construct(
        MusementApiInterface $musementCityApiClient,
        WeatherApiInterface $weatherApiClient,
        ValidatorInterface $validator,
        LoggerInterface $consoleNotifier,
        WeatherForecastRendererInterface $renderer
    ) {
        $this->musementCityApiClient = $musementCityApiClient;
        $this->weatherApiClient = $weatherApiClient;
        $this->validator = $validator;
        $this->consoleNotifier = $consoleNotifier;
        $this->renderer = $renderer;
    }

    /**
     * @param int|null $days
     *
     * @throws MusementCityProcessingException if errors were detected during validation, API call and processing.
     * @throws HttpResponseException if response code is not 200.
     * @throws TransportExceptionInterface When a network error occurs
     * @throws RedirectionExceptionInterface On a 3xx when $throw is true and the "max_redirects" option has been reached
     * @throws ClientExceptionInterface On a 4xx when $throw is true
     * @throws ServerExceptionInterface On a 5xx when $throw is true
     * @throws \InvalidArgumentException if number of days to forecast weather is <= 0.
     */
    public function detect(?int $days = null): void
    {
        $cities = $this->musementCityApiClient->getAllMusementCities();
        $hasErrors = false;

        if (0 === \count($cities)) {
            $this->consoleNotifier->info('There were no cities returned from \'Musement API\'');

            return;
        }

        foreach ($cities as $city) {
            /* @var ConstraintViolationList $errors */
            $errors = $this->validator->validate($city);

            if (\count($errors) > 0) {
                $hasErrors = true;
                /* @phpstan-ignore-next-line */
                $this->consoleNotifier->debug((string) $errors);

                continue;
            }

            try {
                $cityForecast = $this->getCityWeather($city, $days);

                if (null === $cityForecast) {
                    $hasErrors = true;

                    continue;
                }

                $this->renderer->render($cityForecast);
            } catch (\Throwable $exception) {
                $hasErrors = true;
                $this->consoleNotifier->debug($exception->getMessage());
            }
        }

        if ($hasErrors) {
            throw new MusementCityProcessingException();
        }
    }

    /**
     * @param MusementCity $city
     * @param int|null $days
     *
     * @return CityWeatherForecast|null
     *
     * @throws HttpResponseException if response code is not 200.
     * @throws TransportExceptionInterface When a network error occurs
     * @throws RedirectionExceptionInterface On a 3xx when $throw is true and the "max_redirects" option has been reached
     * @throws ClientExceptionInterface On a 4xx when $throw is true
     * @throws ServerExceptionInterface On a 5xx when $throw is true
     */
    private function getCityWeather(MusementCity $city, ?int $days = null): ?CityWeatherForecast
    {
        $cityForecast = $this->weatherApiClient->getCityWeatherForecast(
            sprintf('%f,%f', $city->getLatitude(), $city->getLongitude()),
            $days
        );

        $errors = $this->validator->validate($cityForecast);

        if (\count($errors) > 0) {
            /* @phpstan-ignore-next-line */
            $this->consoleNotifier->debug((string) $errors);

            return null;
        }

        return $cityForecast;
    }
}
