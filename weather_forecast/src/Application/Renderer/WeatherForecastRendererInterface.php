<?php

declare(strict_types=1);

namespace App\Application\Renderer;

use App\Application\DTO\CityWeatherForecast;

interface WeatherForecastRendererInterface
{
    /**
     * @param CityWeatherForecast $weatherForecast
     */
    public function render(CityWeatherForecast $weatherForecast): void;
}
