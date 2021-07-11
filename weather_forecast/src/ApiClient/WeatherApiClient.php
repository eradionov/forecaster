<?php

declare(strict_types=1);

namespace App\ApiClient;

use App\ApiClient\Interfaces\WeatherApiInterface;
use App\DTO\CityWeatherForecast;
use Symfony\Component\HttpFoundation\Request;

class WeatherApiClient extends BaseApiClient implements WeatherApiInterface
{
    private const FORECAST_ENDPOINT = '/v1/forecast.json';

    /**
     * {@inheritdoc}
     */
    public function getCityWeatherForecast(string $latLongGeoPosition, ?int $days = null): CityWeatherForecast
    {
        $latLongGeoPosition = trim($latLongGeoPosition);

        if (0 === \strlen($latLongGeoPosition)) {
            throw new \InvalidArgumentException('Latitude and longitude should be passed.');
        }

        $query = [
            'q' => $latLongGeoPosition,
        ];

        if (null !== $days) {
            $query = array_merge($query, ['days' => $days]);
        }

        return $this->request(
            Request::METHOD_GET,
            self::FORECAST_ENDPOINT,
            CityWeatherForecast::class,
            [
                'query' => $query,
            ]
        );
    }
}
