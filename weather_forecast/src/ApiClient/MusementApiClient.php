<?php

declare(strict_types=1);

namespace App\ApiClient;

use App\ApiClient\Interfaces\MusementApiInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MusementApiClient extends BaseApiClient implements MusementApiInterface
{
    private const API_ENDPOINT = '/api/v3/cities';

    /**
     * @param SerializerInterface $serializer
     * @param HttpClientInterface $musementCityClient
     */
    public function __construct(SerializerInterface $serializer, HttpClientInterface $musementCityClient)
    {
        parent::__construct($serializer, $musementCityClient);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllMusementCities(): array
    {
        return (array) $this->request(
            self::REQUEST_GET,
            self::API_ENDPOINT,
            'App\DTO\MusementCity[]'
        );
    }
}
