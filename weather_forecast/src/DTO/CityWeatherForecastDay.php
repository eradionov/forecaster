<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CityWeatherForecastDay
{
    /**
     * @Assert\NotBlank(message="Weather condition is required.")
     */
    private string $condition;

    private function __construct(string $condition)
    {
        $this->condition = $condition;
    }

    public static function create(string $condition): self
    {
        return new self($condition);
    }

    /**
     * @return string
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * @param string $condition
     */
    public function setCondition(string $condition): void
    {
        $this->condition = $condition;
    }
}
