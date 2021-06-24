<?php

declare(strict_types=1);

namespace App\ApiClient;

use App\ApiClient\Interfaces\WeatherApiInterface;
use App\DTO\CityWeatherForecast;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherApiClient extends BaseApiClient implements WeatherApiInterface
{
    private const API_ENDPOINT = '/v1/forecast.json';

    /**
     * @param SerializerInterface $serializer
     * @param HttpClientInterface $cityForecastClient
     */
    public function __construct(SerializerInterface $serializer, HttpClientInterface $cityForecastClient)
    {
        parent::__construct($serializer, $cityForecastClient);
    }

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
            self::REQUEST_GET,
            self::API_ENDPOINT,
            CityWeatherForecast::class,
            [
                'query' => $query,
            ]
        );
    }
}
