<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\DTO\CityWeatherForecast;
use App\Application\DTO\MusementCity;
use App\Application\Fetcher\MusementCityApiFetcher;
use App\Application\Fetcher\MusementCityForecastApiFetcher;
use App\Application\Renderer\WeatherForecastRendererInterface;
use App\Application\WeatherForecastDetector;
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

    /** @var MusementCityApiFetcher&MockObject */
    private MusementCityApiFetcher $musementCityApiFetcher;

    /** @var MusementCityForecastApiFetcher&MockObject */
    private MusementCityForecastApiFetcher $musementCityForecastApiFetcher;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->notifier = $this->createMock(LoggerInterface::class);
        $this->renderer = $this->createMock(WeatherForecastRendererInterface::class);

        $this->musementCityApiFetcher = $this->createMock(MusementCityApiFetcher::class);
        $this->musementCityForecastApiFetcher = $this->createMock(MusementCityForecastApiFetcher::class);
    }

    public function testNegativeNumberOfForecastDays(): void
    {
        $this->expectExceptionMessage('Number of days to forecast weather should be positive.');
        $weatherForecastDetector = new WeatherForecastDetector(
            $this->musementCityApiFetcher,
            $this->musementCityForecastApiFetcher,
            $this->validator,
            $this->notifier,
            $this->renderer
        );

        $weatherForecastDetector->detect(-5);
    }

    public function testZeroNumberOfForecastDays(): void
    {
        $this->expectExceptionMessage('Number of days to forecast weather should be positive.');
        $weatherForecastDetector = new WeatherForecastDetector(
            $this->musementCityApiFetcher,
            $this->musementCityForecastApiFetcher,
            $this->validator,
            $this->notifier,
            $this->renderer
        );

        $weatherForecastDetector->detect(0);
    }

    public function testInvalidMusementCity(): void
    {
        $this->expectExceptionMessage(self::DEFAULT_ERROR_MESSAGE);

        $this->musementCityApiFetcher->method('fetch')->willReturn([new MusementCity()]);
        $this->validator->method('validate')->willReturn([]);

        $weatherForecastDetector = new WeatherForecastDetector(
            $this->musementCityApiFetcher,
            $this->musementCityForecastApiFetcher,
            $this->validator,
            $this->notifier,
            $this->renderer
        );

        $weatherForecastDetector->detect(self::DEFAULT_FORECAST_DAYS);
    }

    /**
     * @dataProvider getMusementApiResponse
     *
     * @param array{name: string, latitude: float, longitude: float} $responseData
     */
    public function testValidMusementCityWithoutForecast(array $responseData): void
    {
        $this->expectExceptionMessage(self::DEFAULT_ERROR_MESSAGE);

        $this->musementCityApiFetcher->method('fetch')->willReturn([MusementCity::fromArray($responseData)]);
        $this->validator->method('validate')->willReturn([]);

        $weatherForecastDetector = new WeatherForecastDetector(
            $this->musementCityApiFetcher,
            $this->musementCityForecastApiFetcher,
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
     * @param array{name: string, latitude: float, longitude: float} $responseData
     * @param array{city: string, forecasts: array} $forecasts
     */
    public function testValidMusementCityWithForecasts(array $responseData, array $forecasts): void
    {
        $this->musementCityApiFetcher->method('fetch')->willReturn([MusementCity::fromArray($responseData)]);
        $this->validator->method('validate')->willReturn([]);

        $this->musementCityForecastApiFetcher->method('fetch')->willReturn(CityWeatherForecast::fromArray($forecasts));

        $weatherForecastDetector = new WeatherForecastDetector(
            $this->musementCityApiFetcher,
            $this->musementCityForecastApiFetcher,
            $this->validator,
            $this->notifier,
            $this->renderer
        );

        $weatherForecastDetector->detect(self::DEFAULT_FORECAST_DAYS);
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
                [
                    'city' => 'Amsterdam',
                    'forecasts' => ['Sunny', 'Cloudy'],
                ],
            ],
        ];
    }
}
