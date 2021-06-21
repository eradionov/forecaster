<?php

declare(strict_types=1);

namespace App\Tests\Application\Handler;

use App\Application\DTO\CityWeatherForecast;
use App\Application\Factory\MusementCityForecastSerializerFactory;
use App\Application\Handler\MusementCityForecastApiHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MusementCityForecastApiHandlerTest extends TestCase
{
    /** @var HttpClientInterface&MockObject */
    private HttpClientInterface $httpClient;

    /** @var ResponseInterface&MockObject */
    private ResponseInterface $response;

    /** @var SerializerInterface */
    private SerializerInterface $serializer;

    public function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->serializer = MusementCityForecastSerializerFactory::build();
    }

    /**
     * @dataProvider getMusementCityForecastApiResponse
     *
     * @param array<int, array> $responseData
     */
    public function testHttpExceptionResponse(array $responseData): void
    {
        $this->expectExceptionMessage(
            sprintf(
                'Weather forecast request finished with error HTTP code: %d',
                Response::HTTP_INTERNAL_SERVER_ERROR
            )
        );

        $this->response->method('getStatusCode')->willReturn(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->response->method('getContent')->willReturn(json_encode($responseData));

        $this->httpClient->method('request')
            ->willReturn($this->response);

        $apiHandler = new MusementCityForecastApiHandler(
            $this->httpClient,
            $this->serializer,
            ''
        );

        $response = $apiHandler->fetch();
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

        $this->httpClient->method('request')
            ->willReturn($this->response);

        $apiHandler = new MusementCityForecastApiHandler(
            $this->httpClient,
            $this->serializer,
            ''
        );

        $response = $apiHandler->fetch();

        self::assertEquals($weatherForecast, $response);
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
