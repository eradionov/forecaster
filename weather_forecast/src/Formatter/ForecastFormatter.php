<?php

declare(strict_types=1);

namespace App\Formatter;

use App\DTO\MusementCity;

final class ForecastFormatter implements MusementCityWeatherFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function format(MusementCity $city): string
    {
        $forecasts = [];

        foreach ($city->getForecast()->getForecastsDay() as $dailyForecast) {
            $forecasts[] = $dailyForecast->getCondition();
        }

        return sprintf(
            'Processed city %s | %s',
            $city->getName(),
            implode(' - ', $forecasts)
        );
    }
}
