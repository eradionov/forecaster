<?php

declare(strict_types=1);

namespace App\Formatter;

use App\DTO\CityWeatherForecast;

final class ForecastFormatter
{
    /**
     * @param CityWeatherForecast $forecast
     *
     * @return string
     */
    public static function format(CityWeatherForecast $forecast): string
    {
        return sprintf(
            'Processed city %s | %s',
            $forecast->getCity(),
            implode(' - ', $forecast->getForecasts())
        );
    }
}
