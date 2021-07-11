<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class MusementCity
{
    /**
     * @Assert\NotBlank(message="City name is required.")
     */
    private string $name;

    /**
     * @Assert\NotNull(message="Latitude is required.")
     */
    private float $latitude;

    /**
     * @Assert\NotNull(message="Longitude is required.")
     */
    private float $longitude;

    /**
     * @var CityWeatherForecast
     *
     * @Assert\Type(type="CityWeatherForecast", message="Forecast is required.")
     */
    private CityWeatherForecast $forecast;

    /**
     * @param array{name: string, latitude: float, longitude: float} $data
     *
     * @return MusementCity
     */
    public static function fromArray(array $data): self
    {
        $musementCity = new self();
        $musementCity->setName($data['name']);
        $musementCity->setLatitude($data['latitude']);
        $musementCity->setLongitude($data['longitude']);

        return $musementCity;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @return CityWeatherForecast
     */
    public function getForecast(): CityWeatherForecast
    {
        return $this->forecast;
    }

    /**
     * @param CityWeatherForecast $forecast
     */
    public function setForecast(CityWeatherForecast $forecast): void
    {
        $this->forecast = $forecast;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }
}
