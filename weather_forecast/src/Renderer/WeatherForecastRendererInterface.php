<?php

namespace App\Renderer;

use App\DTO\CityWeatherForecast;

interface WeatherForecastRendererInterface
{
    /**
     * @param CityWeatherForecast $weatherForecast
     */
    public function render(CityWeatherForecast $weatherForecast): void;
}
