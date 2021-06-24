<?php

declare(strict_types=1);

namespace App\Tests\WeatherForecast;

use App\ApiClient\Interfaces\MusementApiInterface;
use App\ApiClient\Interfaces\WeatherApiInterface;
use App\ApiClient\MusementApiClient;
use App\ApiClient\WeatherApiClient;
use App\DTO\CityWeatherForecast;
use App\DTO\CityWeatherForecastDay;
use App\DTO\MusementCity;
use App\Renderer\WeatherForecastRendererInterface;
use App\WeatherForecast\WeatherForecastDetector;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class WeatherForecastDetectorTest extends TestCase
{
    private const DEFAULT_FORECAST_DAYS = 2;
    private const DEFAULT_ERROR_MESSAGE = 'Some errors occurred during processing, please see log for details.';

    /** @var ValidatorInterface&MockObject */
    private ValidatorInterface $validator;

    /** @var LoggerInterface&MockObject */
    private LoggerInterface $notifier;

    /** @var WeatherForecastRendererInterface&MockObject */
    private WeatherForecastRendererInterface $renderer;

    /** @var MusementApiInterface&MockObject */
    private MusementApiInterface $musementApiClient;

    /** @var WeatherApiInterface&MockObject */
    private WeatherApiInterface $cityWeatherApiClient;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->notifier = $this->createMock(LoggerInterface::class);
        $this->renderer = $this->createMock(WeatherForecastRendererInterface::class);

        $this->musementApiClient = $this->createMock(MusementApiClient::class);
        $this->cityWeatherApiClient = $this->createMock(WeatherApiClient::class);
    }

    public function testInvalidMusementCity(): void
    {
        $this->expectExceptionMessage(self::DEFAULT_ERROR_MESSAGE);

        $this->musementApiClient->method('getAllMusementCities')->willReturn([new MusementCity()]);
        $this->validator->method('validate')->willReturn([]);

        $weatherForecastDetector = new WeatherForecastDetector(
            $this->musementApiClient,
            $this->cityWeatherApiClient,
            $this->validator,
            $this->notifier,
            $this->renderer
        );

        $weatherForecastDetector->detect(self::DEFAULT_FORECAST_DAYS);
    }

    /**
     * @dataProvider getMusementApiResponseWithoutForecast
     *
     * @param array{name: string, latitude: float, longitude: float, forecast: ?CityWeatherForecast} $responseData
     * @param CityWeatherForecast $forecast
     */
    public function testValidMusementCityWithoutForecast(array $responseData, CityWeatherForecast $forecast): void
    {
        $this->expectExceptionMessage(self::DEFAULT_ERROR_MESSAGE);

        $musementCity = MusementCity::fromArray($responseData);
        $this->musementApiClient->method('getAllMusementCities')->willReturn([$musementCity]);
        $this->cityWeatherApiClient->method('getCityWeatherForecast')->willReturn($forecast);

        $weatherForecastDetector = new WeatherForecastDetector(
            $this->musementApiClient,
            $this->cityWeatherApiClient,
            $this->validator,
            $this->notifier,
            $this->renderer
        );

        $weatherForecastDetector->detect(self::DEFAULT_FORECAST_DAYS);
    }

    /**
     * @doesNotPerformAssertions
     * @dataProvider getMusementApiResponse
     *
     * @param array{name: string, latitude: float, longitude: float, forecast: ?CityWeatherForecast} $responseData
     * @param CityWeatherForecast $forecasts
     */
    public function testValidMusementCityWithForecasts(array $responseData, CityWeatherForecast $forecasts): void
    {
        $this->musementApiClient->method('getAllMusementCities')->willReturn([MusementCity::fromArray($responseData)]);
        $this->validator->method('validate')->willReturn([]);

        $this->cityWeatherApiClient->method('getCityWeatherForecast')->willReturn($forecasts);

        $weatherForecastDetector = new WeatherForecastDetector(
            $this->musementApiClient,
            $this->cityWeatherApiClient,
            $this->validator,
            $this->notifier,
            $this->renderer
        );

        $weatherForecastDetector->detect(self::DEFAULT_FORECAST_DAYS);
    }

    /**
     * @return array<int, array>
     */
    public function getMusementApiResponseWithoutForecast(): array
    {
        return [
            [
                [
                    'name' => 'Amsterdam',
                    'latitude' => 52.374,
                    'longitude' => 4.9,
                ],
                CityWeatherForecast::fromArray(
                    [
                        'forecastsDay' => [],
                    ]
                ),
            ],
        ];
    }

    /**
     * @return array<int, array>
     */
    public function getMusementApiResponse(): array
    {
        return [
            [
                [
                    'name' => 'Amsterdam',
                    'latitude' => 52.374,
                    'longitude' => 4.9,
                ],
                CityWeatherForecast::fromArray(
                    [
                        'forecastsDay' => [
                            CityWeatherForecastDay::create('Sunny'),
                            CityWeatherForecastDay::create('Cloudy'),
                        ],
                    ]
                ),
            ],
        ];
    }
}
