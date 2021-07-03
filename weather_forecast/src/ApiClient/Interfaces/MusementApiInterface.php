<?php

namespace App\ApiClient\Interfaces;

use App\DTO\MusementCity;
use App\Exception\HttpResponseException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

interface MusementApiInterface
{
    /**
     * @return array<MusementCity>
     *
     * @throws HttpResponseException if response code is not 200 or 201
     * @throws \InvalidArgumentException if invalid request method passed.
     * @throws TransportExceptionInterface When a network error occurs
     * @throws RedirectionExceptionInterface On a 3xx when $throw is true and the "max_redirects" option has been reached
     * @throws ClientExceptionInterface On a 4xx when $throw is true
     * @throws ServerExceptionInterface On a 5xx when $throw is true
     */
    public function getAllMusementCities(): array;
}
