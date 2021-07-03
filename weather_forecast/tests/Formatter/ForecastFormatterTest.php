<?php

declare(strict_types=1);

namespace App\Tests\Formatter;

use App\DTO\CityWeatherForecast;
use App\Formatter\ForecastFormatter;
use PHPUnit\Framework\TestCase;

final class ForecastFormatterTest extends TestCase
{
    public function testFormatterWithTwoDaysResponse(): void
    {
        $cityWeatherForecast = new CityWeatherForecast();
        $cityWeatherForecast->setForecasts(['Sunny', 'Cloudy']);
        $cityWeatherForecast->setCity('New-York');
        $forecastFormatter = new ForecastFormatter();

        self::assertSame(
            'Processed city New-York | Sunny - Cloudy',
            $forecastFormatter->format($cityWeatherForecast->toArray())
        );
    }

    public function testFormatterWithThreeDaysResponse(): void
    {
        $cityWeatherForecast = new CityWeatherForecast();
        $cityWeatherForecast->setForecasts(['Sunny', 'Cloudy', 'Snowy']);
        $cityWeatherForecast->setCity('New-York');
        $forecastFormatter = new ForecastFormatter();

        self::assertSame(
            'Processed city New-York | Sunny - Cloudy - Snowy',
            $forecastFormatter->format($cityWeatherForecast->toArray())
        );
    }

    public function testFormatterWithEmptyDataResponse(): void
    {
        $cityWeatherForecast = new CityWeatherForecast();
        $cityWeatherForecast->setForecasts([]);
        $cityWeatherForecast->setCity('');
        $forecastFormatter = new ForecastFormatter();

        self::assertSame('Processed city  | ', $forecastFormatter->format($cityWeatherForecast->toArray()));
    }
}
