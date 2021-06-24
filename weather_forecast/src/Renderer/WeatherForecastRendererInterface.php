<?php

namespace App\Renderer;

use App\DTO\MusementCity;

interface WeatherForecastRendererInterface
{
    /**
     * @param MusementCity $city
     */
    public function render(MusementCity $city): void;
}
