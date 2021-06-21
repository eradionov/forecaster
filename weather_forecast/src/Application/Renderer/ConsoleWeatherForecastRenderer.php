<?php

declare(strict_types=1);

namespace App\Application\Renderer;

use App\Application\DTO\CityWeatherForecast;
use App\Application\Formatter\ForecastFormatter;
use Psr\Log\LoggerInterface;

final class ConsoleWeatherForecastRenderer implements WeatherForecastRendererInterface
{
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param CityWeatherForecast $weatherForecast
     */
    public function render(CityWeatherForecast $weatherForecast): void
    {
        $this->logger->notice(ForecastFormatter::format($weatherForecast));
    }
}