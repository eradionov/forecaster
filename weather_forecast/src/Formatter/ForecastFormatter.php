<?php

declare(strict_types=1);

namespace App\Formatter;

use App\DTO\MusementCity;
use App\Exception\InvalidFormatException;

final class ForecastFormatter implements MusementCityWeatherFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function format(MusementCity $city): string
    {
        return sprintf(
            'Processed city %s | %s',
            $city->getName(),
            implode(' - ', $city->getForecast()->getForecasts() ?? [])
        );
    }
}
