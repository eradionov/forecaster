<?php

declare(strict_types=1);

namespace App\ApiClient;

use App\ApiClient\Interfaces\MusementApiInterface;
use Symfony\Component\HttpFoundation\Request;

class MusementApiClient extends BaseApiClient implements MusementApiInterface
{
    private const CITIES_ENDPOINT = '/api/v3/cities';

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
