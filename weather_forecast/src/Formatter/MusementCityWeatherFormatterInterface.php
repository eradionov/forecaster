<?php

namespace App\Formatter;

use App\DTO\MusementCity;
use App\Exception\InvalidFormatException;

interface MusementCityWeatherFormatterInterface
{
    /**
     * @param MusementCity $city
     *
     * @return string
     *
     * @throws InvalidFormatException if invalid array format passed
     */
    public function format(MusementCity $city): string;
}
