<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CityWeatherForecast
{
    /**
     * @var array<CityWeatherForecastDay>
     *
     * @Assert\NotBlank(message="Daily forecasts is required")
     */
    private array $forecastsDay;

    /**
     * @param array{forecastsDay: array<CityWeatherForecastDay>} $data
     *
     * @return CityWeatherForecast
     */
    public static function fromArray(array $data): self
    {
        $cityWeatherForecast = new self();
        $cityWeatherForecast->setForecastsDay($data['forecastsDay']);

        return $cityWeatherForecast;
    }

    /**
     * @return array<CityWeatherForecastDay>
     */
    public function getForecastsDay(): array
    {
        return $this->forecastsDay;
    }

    /**
     * @param array<CityWeatherForecastDay> $forecastsDay
     */
    public function setForecastsDay(array $forecastsDay): void
    {
        $this->forecastsDay = $forecastsDay;
    }
}
