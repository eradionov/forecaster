<?php

declare(strict_types=1);

namespace App\Renderer;

use App\DTO\CityWeatherForecast;
use App\Exception\InvalidFormatException;
use App\Formatter\ArrayToStringFormatterInterface;
use Psr\Log\LoggerInterface;

final class ConsoleWeatherForecastRenderer implements WeatherForecastRendererInterface
{
    private LoggerInterface $consoleNotifier;
    private ArrayToStringFormatterInterface $formatter;

    /**
     * @param LoggerInterface $consoleNotifier
     * @param ArrayToStringFormatterInterface $formatter
     */
    public function __construct(LoggerInterface $consoleNotifier, ArrayToStringFormatterInterface $formatter)
    {
        $this->consoleNotifier = $consoleNotifier;
        $this->formatter = $formatter;
    }

    /**
     * @param CityWeatherForecast $weatherForecast
     *
     * @throws InvalidFormatException if data, passed into formatter has incorrect format.
     */
    public function render(CityWeatherForecast $weatherForecast): void
    {
        $this->consoleNotifier->notice($this->formatter->format($weatherForecast->toArray()));
    }
}
