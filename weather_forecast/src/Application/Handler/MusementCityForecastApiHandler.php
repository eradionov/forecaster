<?php

declare(strict_types=1);

namespace App\Application\Handler;

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

class MusementCityForecastApiHandler implements ApiRequestHandlerInterface
{
    private const API_CITY_FORECAST_ENDPOINT = '/forecast.json';

    private HttpClientInterface $httpClient;
    private SerializerInterface $serializer;
    private string $endpoint;

    /**
     * @param HttpClientInterface $httpClient
     * @param SerializerInterface $serializer
     * @param string $url
     */
    public function __construct(HttpClientInterface $httpClient, SerializerInterface $serializer, string $url)
    {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
        $this->endpoint = rtrim($url, '/').self::API_CITY_FORECAST_ENDPOINT;
    }

    /**
     * @param RequestParams|null $requestParams
     *
     * @return CityWeatherForecast
     *
     * @throws HttpResponseException if response code is not 200.
     * @throws TransportExceptionInterface When a network error occurs
     * @throws RedirectionExceptionInterface On a 3xx when $throw is true and the "max_redirects" option has been reached
     * @throws ClientExceptionInterface On a 4xx when $throw is true
     * @throws ServerExceptionInterface On a 5xx when $throw is true
     */
    public function fetch(RequestParams $requestParams = null): CityWeatherForecast
    {
        $response = $this->httpClient->request(
            'GET',
            $this->endpoint,
            $requestParams ? $requestParams->toArray() : []
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
