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

        self::assertSame(
            sprintf(
                'Processed city New-York | Sunny - Cloudy',
            ),
            ForecastFormatter::format($cityWeatherForecast)
        );
    }

    public function testFormatterWithThreeDaysResponse(): void
    {
        $cityWeatherForecast = new CityWeatherForecast();
        $cityWeatherForecast->setForecasts(['Sunny', 'Cloudy', 'Snowy']);
        $cityWeatherForecast->setCity('New-York');

        self::assertSame(
            sprintf(
                'Processed city New-York | Sunny - Cloudy - Snowy',
            ),
            ForecastFormatter::format($cityWeatherForecast)
        );
    }

    public function testFormatterWithEmptyDataResponse(): void
    {
        $cityWeatherForecast = new CityWeatherForecast();
        $cityWeatherForecast->setForecasts([]);
        $cityWeatherForecast->setCity('');

        self::assertSame(
            sprintf(
                'Processed city  | ',
            ),
            ForecastFormatter::format($cityWeatherForecast)
        );
    }
}
