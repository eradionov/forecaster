<?php

declare(strict_types=1);

namespace App\Tests\Application\Serializer\Normalizer;

use App\Application\DTO\CityWeatherForecast;
use App\Application\Serializer\Normalizer\MusementCityForecastDenormalizer;
use PHPUnit\Framework\TestCase;

final class MusementCityForecastDenormalizerTest extends TestCase
{
    /**
     * @dataProvider getResponseWithForecasts
     *
     * @param array<int, array> $cityForecasts
     */
    public function testWithForecasts(array $cityForecasts): void
    {
        $type = CityWeatherForecast::class;
        $response = (new MusementCityForecastDenormalizer())->denormalize($cityForecasts, $type);

        self::assertEquals(['Sunny', 'Cloudy', 'Windy'], $response->getCityForecastDays());
        self::assertEquals('New-York', $response->getCity());
    }

    /**
     * @dataProvider getResponseWithoutForecasts
     *
     * @param array<int, array> $cityForecasts
     */
    public function testWithoutForecasts(array $cityForecasts): void
    {
        $type = CityWeatherForecast::class;
        $response = (new MusementCityForecastDenormalizer())->denormalize($cityForecasts, $type);

        self::assertEquals([], $response->getCityForecastDays());
        self::assertEquals('New-York', $response->getCity());
    }

    /**
     * @return array<int, array>
     */
    public function getResponseWithForecasts(): array
    {
        return [
            [
                [
                    'location' => [
                        'name' => 'New-York',
                    ],
                    'forecast' => [
                        'forecastday' => [
                            [
                                'day' => [
                                    'condition' => [
                                        'text' => 'Sunny',
                                    ],
                                ],
                            ],
                            [
                                'day' => [
                                    'condition' => [
                                        'text' => 'Cloudy',
                                    ],
                                ],
                            ],
                            [
                                'day' => [
                                    'condition' => [
                                        'text' => 'Windy',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array>
     */
    public function getResponseWithoutForecasts(): array
    {
        return [
            [
                [
                    'location' => [
                        'name' => 'New-York',
                    ],
                ],
            ],
        ];
    }
}
