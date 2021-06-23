<?php

declare(strict_types=1);

namespace App\Renderer;

use App\DTO\CityWeatherForecast;
use App\Formatter\ForecastFormatter;
use Psr\Log\LoggerInterface;

final class ConsoleWeatherForecastRenderer implements WeatherForecastRendererInterface
{
    private LoggerInterface $consoleNotifier;

    /**
     * @param LoggerInterface $consoleNotifier
     */
    public function __construct(LoggerInterface $consoleNotifier)
    {
        $this->consoleNotifier = $consoleNotifier;
    }

    /**
     * @param CityWeatherForecast $weatherForecast
     */
    public function render(CityWeatherForecast $weatherForecast): void
    {
        $this->consoleNotifier->notice(ForecastFormatter::format($weatherForecast));
    }
}
