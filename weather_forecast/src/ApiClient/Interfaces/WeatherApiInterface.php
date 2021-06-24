<?php

namespace App\ApiClient\Interfaces;

use App\DTO\CityWeatherForecast;
use App\Exception\HttpResponseException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

interface WeatherApiInterface
{
    /**
     * @param string $latLongGeoPosition latitude and longitude position in a string format (23.12,12.12)
     * @param int|null $days Number of days for forecast fetching
     *
     * @return CityWeatherForecast
     *
     * @throws HttpResponseException if response code is not 200.
     * @throws \InvalidArgumentException if invalid parameters passed
     * @throws TransportExceptionInterface When a network error occurs
     * @throws RedirectionExceptionInterface On a 3xx when $throw is true and the "max_redirects" option has been reached
     * @throws ClientExceptionInterface On a 4xx when $throw is true
     * @throws ServerExceptionInterface On a 5xx when $throw is true
     */
    public function getCityWeatherForecast(string $latLongGeoPosition, ?int $days = null): CityWeatherForecast;
}
