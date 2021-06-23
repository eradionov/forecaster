<?php

declare(strict_types=1);

namespace App\Fetcher;

use App\Exception\HttpResponseException;
use App\Utils\RequestParams;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MusementCityApiFetcher implements ApiRequestFetcherInterface
{
    private const API_CITIES_ENDPOINT = '/api/v3/cities';

    private HttpClientInterface $musementCityClient;
    private SerializerInterface $apiMusementSerialiser;

    /**
     * @param HttpClientInterface $musementCityClient
     * @param SerializerInterface $apiMusementSerialiser
     */
    public function __construct(HttpClientInterface $musementCityClient, SerializerInterface $apiMusementSerialiser)
    {
        $this->musementCityClient = $musementCityClient;
        $this->apiMusementSerialiser = $apiMusementSerialiser;
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
        $response = $this->musementCityClient->request(
            'GET',
            self::API_CITIES_ENDPOINT,
            $requestParams !== null ? $requestParams->toArray() : []
        );

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new HttpResponseException(sprintf('Cities request finished with error HTTP code: %d', $response->getStatusCode()));
        }

        return $this->apiMusementSerialiser->deserialize(
            $response->getContent(), 'App\DTO\MusementCity[]', 'json'
        );
    }
}
