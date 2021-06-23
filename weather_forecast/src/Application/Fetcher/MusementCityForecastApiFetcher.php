<?php

declare(strict_types=1);

namespace App\Application\Fetcher;

use App\Application\DTO\CityWeatherForecast;
use App\Exception\HttpResponseException;
use App\Utils\RequestParams;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MusementCityForecastApiFetcher implements ApiRequestFetcherInterface
{
    private const API_CITY_FORECAST_ENDPOINT = '/v1/forecast.json';

    private HttpClientInterface $cityForecastClient;
    private SerializerInterface $serializer;

    /**
     * @param HttpClientInterface $cityForecastClient
     * @param SerializerInterface $serializer
     */
    public function __construct(HttpClientInterface $cityForecastClient, SerializerInterface $serializer)
    {
        $this->cityForecastClient = $cityForecastClient;
        $this->serializer = $serializer;
    }

    /**
     * @param RequestParams|null $requestParams
     *
     * @return mixed
     *
     * @throws HttpResponseException if response code is not 200.
     * @throws TransportExceptionInterface When a network error occurs
     * @throws RedirectionExceptionInterface On a 3xx when $throw is true and the "max_redirects" option has been reached
     * @throws ClientExceptionInterface On a 4xx when $throw is true
     * @throws ServerExceptionInterface On a 5xx when $throw is true
     */
    public function fetch(RequestParams $requestParams = null)
    {
        $response = $this->cityForecastClient->request(
            'GET',
            self::API_CITY_FORECAST_ENDPOINT,
            $requestParams !== null ? $requestParams->toArray() : []
        );

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new HttpResponseException(sprintf('Weather forecast request finished with error HTTP code: %d', $response->getStatusCode()));
        }

        return $this->serializer->deserialize(
            $response->getContent(),
            CityWeatherForecast::class,
            'json'
        );
    }
}
