<?php

declare(strict_types=1);

namespace App\Renderer;

use App\DTO\MusementCity;
use App\Exception\InvalidFormatException;
use App\Formatter\MusementCityWeatherFormatterInterface;
use Psr\Log\LoggerInterface;

final class ConsoleWeatherForecastRenderer implements WeatherForecastRendererInterface
{
    private LoggerInterface $consoleNotifier;
    private MusementCityWeatherFormatterInterface $formatter;

    /**
     * @param LoggerInterface $consoleNotifier
     * @param MusementCityWeatherFormatterInterface $formatter
     */
    public function __construct(LoggerInterface $consoleNotifier, MusementCityWeatherFormatterInterface $formatter)
    {
        $this->consoleNotifier = $consoleNotifier;
        $this->formatter = $formatter;
    }

    /**
     * @param MusementCity $city
     *
     * @throws InvalidFormatException if data, passed into formatter has incorrect format.
     */
    public function render(MusementCity $city): void
    {
        $this->consoleNotifier->notice($this->formatter->format($city));
    }
}
