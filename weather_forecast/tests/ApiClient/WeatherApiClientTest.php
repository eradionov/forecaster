<?php

declare(strict_types=1);

namespace App\Tests\ApiClient;

use App\ApiClient\WeatherApiClient;
use App\DTO\CityWeatherForecast;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class WeatherApiClientTest extends TestCase
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

    public function testInvalidArgumentResponse(): void
    {
        $this->expectExceptionMessage('Latitude and longitude should be passed.');

        $weatherApiClient = new WeatherApiClient($this->serializer, $this->httpClient);
        $weatherApiClient->getCityWeatherForecast('');
    }

    /**
     * @dataProvider getMusementCityForecastApiResponse
     *
     * @param array<int, array> $responseData
     * @param CityWeatherForecast $weatherForecast
     */
    public function testResponseWithData(array $responseData, CityWeatherForecast $weatherForecast): void
    {
        $this->response->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $this->response->method('getContent')->willReturn(json_encode($responseData));
        $this->serializer->method('deserialize')->willReturn($weatherForecast);

        $this->httpClient->method('request')
            ->willReturn($this->response);

        $apiFetcher = new WeatherApiClient($this->serializer, $this->httpClient);
        $response = $apiFetcher->getCityWeatherForecast('12.12,23.23');

        self::assertEquals($weatherForecast, $response);
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

        $apiFetcher = new WeatherApiClient($this->serializer, $this->httpClient);

        $apiFetcher->getCityWeatherForecast('12.12,23.12', 2);
    }

    /**
     * @return array<int, array>
     */
    public function getMusementCityForecastApiResponse(): array
    {
        return [
            [
                [
                    'location' => [
                        'name' => 'Amsterdam',
                    ],
                    'forecast' => [
                        'forecastday' => [
                            [
                                'day' => [
                                    'condition' => [
                                        'text' => 'Sunny',
                                    ],
                                ],
                            ],
                            [
                                'day' => [
                                    'condition' => [
                                        'text' => 'Cloudy',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                CityWeatherForecast::fromArray([
                    'city' => 'Amsterdam',
                    'forecasts' => ['Sunny', 'Cloudy'],
                ]),
            ],
        ];
    }
}
