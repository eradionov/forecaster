<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\DTO\CityWeatherForecast;
use App\Application\DTO\MusementCity;
use App\Application\Handler\ApiRequestHandlerInterface;
use App\Application\Handler\MusementCityApiHandler;
use App\Application\Handler\MusementCityForecastApiHandler;
use App\Application\Renderer\WeatherForecastRendererInterface;
use App\Application\Repository\ApiHandlerRepositoryInterface;
use App\Application\WeatherForecastDetector;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class WeatherForecastDetectorTest extends TestCase
{
    private const DEFAULT_FORECAST_DAYS = 2;
    private const DEFAULT_ERROR_MESSAGE = 'Some errors occurred during processing, please see log for details.';

    /** @var ApiHandlerRepositoryInterface&MockObject */
    private ApiHandlerRepositoryInterface $musementApiRepository;

    /** @var ValidatorInterface&MockObject */
    private ValidatorInterface $validator;

    /** @var LoggerInterface&MockObject */
    private LoggerInterface $notifier;

    /** @var WeatherForecastRendererInterface&MockObject */
    private WeatherForecastRendererInterface $renderer;

    /** @var ApiRequestHandlerInterface&MockObject */
    private ApiRequestHandlerInterface $musementCityApiHandler;

    /** @var ApiRequestHandlerInterface&MockObject */
    private ApiRequestHandlerInterface $musementCityForecastApiHandler;

    protected function setUp(): void
    {
        $this->musementApiRepository = $this->createMock(ApiHandlerRepositoryInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->notifier = $this->createMock(LoggerInterface::class);
        $this->renderer = $this->createMock(WeatherForecastRendererInterface::class);

        $this->musementCityApiHandler = $this->createMock(MusementCityApiHandler::class);
        $this->musementCityForecastApiHandler = $this->createMock(MusementCityForecastApiHandler::class);

        $this->musementApiRepository->method('getMusementCityApiHandler')
            ->willReturn($this->musementCityApiHandler);

        $this->musementApiRepository->method('getMusementCityForecaseApiHandler')
            ->willReturn($this->musementCityForecastApiHandler);
    }

    public function testNegativeNumberOfForecastDays(): void
    {
        $this->expectExceptionMessage('Number of days to forecast weather should be positive.');
        $weatherForecastDetector = new WeatherForecastDetector(
            $this->musementApiRepository,
            $this->validator,
            $this->notifier,
            $this->renderer,
            ''
        );

        $weatherForecastDetector->detect(-5);
    }

    public function testZeroNumberOfForecastDays(): void
    {
        $this->expectExceptionMessage('Number of days to forecast weather should be positive.');
        $weatherForecastDetector = new WeatherForecastDetector(
            $this->musementApiRepository,
            $this->validator,
            $this->notifier,
            $this->renderer,
            ''
        );

        $weatherForecastDetector->detect(0);
    }

    public function testInvalidMusementCity(): void
    {
        $this->expectExceptionMessage(self::DEFAULT_ERROR_MESSAGE);

        $this->musementCityApiHandler->method('fetch')->willReturn([new MusementCity()]);
        $this->validator->method('validate')->willReturn([]);

        $weatherForecastDetector = new WeatherForecastDetector(
            $this->musementApiRepository,
            $this->validator,
            $this->notifier,
            $this->renderer,
            ''
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

        $this->musementCityApiHandler->method('fetch')->willReturn([MusementCity::fromArray($responseData)]);
        $this->validator->method('validate')->willReturn([]);

        $weatherForecastDetector = new WeatherForecastDetector(
            $this->musementApiRepository,
            $this->validator,
            $this->notifier,
            $this->renderer,
            ''
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
        $this->musementCityApiHandler->method('fetch')->willReturn([MusementCity::fromArray($responseData)]);
        $this->validator->method('validate')->willReturn([]);

        $this->musementCityForecastApiHandler->method('fetch')->willReturn(CityWeatherForecast::fromArray($forecasts));

        $weatherForecastDetector = new WeatherForecastDetector(
            $this->musementApiRepository,
            $this->validator,
            $this->notifier,
            $this->renderer,
            ''
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
