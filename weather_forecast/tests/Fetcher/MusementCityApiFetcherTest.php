<?php

declare(strict_types=1);

namespace App\Tests\Fetcher;

use App\DTO\MusementCity;
use App\Factory\MusementApiSerializerFactory;
use App\Fetcher\MusementCityApiFetcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MusementCityApiFetcherTest extends TestCase
{
    /** @var HttpClientInterface&MockObject */
    private HttpClientInterface $httpClient;

    /** @var ResponseInterface&MockObject */
    private ResponseInterface $response;

    public function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
    }

    public function testEmptyApiResponse(): void
    {
        $this->response->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $this->response->method('getContent')->willReturn('');

        $this->httpClient->method('request')
            ->willReturn($this->response);

        $apiFetcher = new MusementCityApiFetcher($this->httpClient, $this->createMock(SerializerInterface::class));

        $response = $apiFetcher->fetch();

        self::assertNull($response);
    }

    public function testHttpExceptionResponse(): void
    {
        $this->expectExceptionMessage(
            sprintf(
                'Cities request finished with error HTTP code: %d',
                Response::HTTP_INTERNAL_SERVER_ERROR
            )
        );

        $this->response->method('getStatusCode')->willReturn(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->response->method('getContent')->willReturn('');

        $this->httpClient->method('request')
            ->willReturn($this->response);

        $apiFetcher = new MusementCityApiFetcher($this->httpClient, $this->createMock(SerializerInterface::class));

        $apiFetcher->fetch();
    }

    /**
     * @dataProvider getMusementApiResponse
     *
     * @param array<int, array{name: string, longitude: float, latitude: float}> $responseData
     */
    public function testResponseWithData(array $responseData): void
    {
        $musementCityMock = MusementCity::fromArray($responseData[0]);

        $this->response->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $this->response->method('getContent')->willReturn(json_encode($responseData));

        $this->httpClient->method('request')
            ->willReturn($this->response);

        $apiFetcher = new MusementCityApiFetcher($this->httpClient, MusementApiSerializerFactory::build());

        $response = $apiFetcher->fetch();

        self::assertNotEmpty($response);
        self::assertEquals($musementCityMock, $response[0]);
    }

    /**
     * @return array<int, array>
     */
    public function getMusementApiResponse(): array
    {
        return [
            [
                [
                    [
                        'name' => 'Amsterdam',
                        'latitude' => 52.374,
                        'longitude' => 4.9,
                    ],
                ],
            ],
        ];
    }
}
