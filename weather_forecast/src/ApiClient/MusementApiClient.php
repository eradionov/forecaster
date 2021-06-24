<?php

declare(strict_types=1);

namespace App\ApiClient;

use App\ApiClient\Interfaces\MusementApiInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MusementApiClient extends BaseApiClient implements MusementApiInterface
{
    private const CITIES_ENDPOINT = '/api/v3/cities';

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
            Request::METHOD_GET,
            self::CITIES_ENDPOINT,
            'App\DTO\MusementCity[]'
        );
    }
}
