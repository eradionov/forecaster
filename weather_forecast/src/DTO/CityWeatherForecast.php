<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CityWeatherForecast
{
    /**
     * @var array<int, string>
     *
     * @Assert\NotBlank(message="City forecasts are required.")
     */
    private array $forecasts;

    /**
     * @param array{city: string, forecasts: array<int, string>} $data
     *
     * @return CityWeatherForecast
     */
    public static function fromArray(array $data): self
    {
        $cityWeatherForecast = new self();
        $cityWeatherForecast->setForecasts($data['forecasts']);

        return $cityWeatherForecast;
    }

    /**
     * @return array<int, string>
     */
    public function getForecasts(): array
    {
        return $this->forecasts;
    }

    /**
     * @param array<int, string> $forecasts
     */
    public function setForecasts(array $forecasts): void
    {
        $this->forecasts = $forecasts;
    }
}
