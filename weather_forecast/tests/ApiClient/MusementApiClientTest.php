<?php

declare(strict_types=1);

namespace App\Tests\ApiClient;

use App\ApiClient\MusementApiClient;
use App\DTO\CityWeatherForecast;
use App\DTO\CityWeatherForecastDay;
use App\DTO\MusementCity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MusementApiClientTest extends TestCase
{
    /** @var HttpClientInterface&MockObject */
    private HttpClientInterface $httpClient;

    /** @var ResponseInterface&MockObject */
    private ResponseInterface $response;

    /** @var SerializerInterface&MockObject */
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    public function testEmptyApiResponse(): void
    {
        $this->response->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $this->response->method('getContent')->willReturn('');

        $this->httpClient->method('request')
            ->willReturn($this->response);

        $apiFetcher = new MusementApiClient($this->serializer, $this->httpClient);

        self::assertEmpty($apiFetcher->getAllMusementCities());
    }

    public function testHttpExceptionResponse(): void
    {
        $this->expectExceptionMessage(
            sprintf(
                'Request finished with error HTTP code: %d',
                Response::HTTP_INTERNAL_SERVER_ERROR
            )
        );

        $this->response->method('getStatusCode')->willReturn(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->response->method('getContent')->willReturn('');

        $this->httpClient->method('request')
            ->willReturn($this->response);

        $apiFetcher = new MusementApiClient($this->serializer, $this->httpClient);

        $apiFetcher->getAllMusementCities();
    }

    /**
     * @dataProvider getMusementApiResponse
     *
     * @param array<int, array{name: string, longitude: float, latitude: float, forecast: CityWeatherForecast}> $responseData
     */
    public function testResponseWithData(array $responseData): void
    {
        $musementCityMock = MusementCity::fromArray($responseData[0]);

        $this->response->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $this->response->method('getContent')->willReturn(json_encode($responseData));
        $this->serializer->method('deserialize')->willReturn([$musementCityMock]);

        $this->httpClient->method('request')
            ->willReturn($this->response);

        $apiFetcher = new MusementApiClient($this->serializer, $this->httpClient);

        $response = $apiFetcher->getAllMusementCities();

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
                        'forecast' => CityWeatherForecast::fromArray(
                            [
                                'forecastsDay' => [
                                    CityWeatherForecastDay::create('Snowy'),
                                    CityWeatherForecastDay::create('Rainy'),
                                    CityWeatherForecastDay::create('Cloudy'),
                                ],
                            ]
                        ),
                    ],
                ],
            ],
        ];
    }
}
