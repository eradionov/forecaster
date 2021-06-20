<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\DTO\CityWeatherForecast;
use App\Application\DTO\MusementCity;
use App\Application\Formatter\ForecastFormatter;
use App\Application\Repository\ApiHandlerRepositoryInterface;
use App\Utils\RequestParams;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class WeatherForecastIdentifier
{
    private const REQUESTED_FORECAST_DAYS = 2;
    private const ERROR_MESSAGE = '<error>Processing of response from \'%s\' fails due to validation error.</error>Please see log for details';

    private ApiHandlerRepositoryInterface $musementApiRepository;
    private ValidatorInterface $validator;
    private LoggerInterface $notifier;
    private string $key;

    /**
     * @param ApiHandlerRepositoryInterface $musementApiRepository
     * @param ValidatorInterface $validator
     * @param LoggerInterface $notifier
     * @param string $key
     */
    public function __construct(
        ApiHandlerRepositoryInterface $musementApiRepository,
        ValidatorInterface $validator,
        LoggerInterface $notifier,
        string $key
    ) {
        $this->musementApiRepository = $musementApiRepository;
        $this->validator = $validator;
        $this->key = $key;
        $this->notifier = $notifier;
    }

    public function displayCitiesWithWeatherForecast(): void
    {
        $cities = $this->musementApiRepository->getMusementCityApiHandler()->fetch();

        if (count($cities) === 0) {
            $this->notifier->info('There were no cities returned from \'Musement API\'');

            return;
        }

        foreach ($cities as $city) {
            /* @var ConstraintViolationList $errors */
            $errors = $this->validator->validate($city);

            if (count($errors) > 0) {
                $this->notifier->error(sprintf(self::ERROR_MESSAGE, 'Musement API'));

                /* @phpstan-ignore-next-line */
                $this->notifier->debug((string) $errors);

                continue;
            }

            $cityForecast = $this->getCityForecast($city);

            if (!$cityForecast) {
                continue;
            }

            $this->notifier->notice(ForecastFormatter::format($cityForecast));
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
            $this->notifier->error(sprintf(self::ERROR_MESSAGE, 'Weather API'));

            /* @phpstan-ignore-next-line */
            $this->notifier->debug((string) $errors);

            return null;
        }

        return $cityWeatherForecast;
    }
}
