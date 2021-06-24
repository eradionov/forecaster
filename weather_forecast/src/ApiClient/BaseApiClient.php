<?php

declare(strict_types=1);

namespace App\ApiClient;

use App\Exception\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class BaseApiClient
{
    protected const RESPONSE_FORMAT_JSON = 'json';

    private const AVAILABLE_RESPONSE_FORMATS = [
        self::RESPONSE_FORMAT_JSON,
    ];

    private SerializerInterface $serializer;
    private HttpClientInterface $httpClient;

    /**
     * @param SerializerInterface $serializer
     * @param HttpClientInterface $httpClient
     */
    public function __construct(SerializerInterface $serializer, HttpClientInterface $httpClient)
    {
        $this->serializer = $serializer;
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $requestMethod
     * @param string $deserializeType
     * @param array<string, mixed> $params
     * @param string $responseFormat
     *
     * @return mixed
     *
     * @throws HttpResponseException if response code is not 200 or 201
     * @throws \InvalidArgumentException if invalid request method passed.
     * @throws TransportExceptionInterface When a network error occurs
     * @throws RedirectionExceptionInterface On a 3xx when $throw is true and the "max_redirects" option has been reached
     * @throws ClientExceptionInterface On a 4xx when $throw is true
     * @throws ServerExceptionInterface On a 5xx when $throw is true
     */
    final protected function request(
        string $requestMethod,
        string $uri,
        string $deserializeType,
        array $params = [],
        string $responseFormat = self::RESPONSE_FORMAT_JSON
    ) {
        if (!\in_array($responseFormat, self::AVAILABLE_RESPONSE_FORMATS, true)) {
            throw new \InvalidArgumentException(sprintf('Response format \'%s\' in not available. Please use one of \'%s\'', $requestMethod, implode(', ', self::AVAILABLE_RESPONSE_FORMATS)));
        }

        $response = $this->httpClient->request($requestMethod, $uri, $params);

        if (!\in_array($response->getStatusCode(), [Response::HTTP_OK, Response::HTTP_CREATED], true)) {
            throw new HttpResponseException(sprintf('Request finished with error HTTP code: %d', $response->getStatusCode()));
        }

        return $this->serializer->deserialize(
            $response->getContent(),
            $deserializeType,
            $responseFormat
        );
    }
}
