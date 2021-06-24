<?php

declare(strict_types=1);

namespace App\Tests\Serializer\Denormalizer;

use App\DTO\CityWeatherForecast;
use App\DTO\CityWeatherForecastDay;
use App\Serializer\Denormalizer\MusementCityForecastDenormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class MusementCityForecastDenormalizerTest extends TestCase
{
    /**
     * @dataProvider getResponseWithForecasts
     *
     * @param array<int, array> $cityForecasts
     * @param CityWeatherForecast $forecastsResponseSample
     */
    public function testWithForecasts(array $cityForecasts, CityWeatherForecast $forecastsResponseSample): void
    {
        $type = CityWeatherForecast::class;
        $response = (new MusementCityForecastDenormalizer(new ObjectNormalizer()))->denormalize($cityForecasts, $type);

        self::assertEquals($forecastsResponseSample->getForecastsDay(), $response->getForecastsDay());
    }

    /**
     * @dataProvider getResponseWithoutForecasts
     *
     * @param array<int, array> $cityForecasts
     * @param CityWeatherForecast $forecast
     */
    public function testWithoutForecasts(array $cityForecasts, CityWeatherForecast $forecast): void
    {
        $type = CityWeatherForecast::class;
        $response = (new MusementCityForecastDenormalizer(new ObjectNormalizer()))->denormalize($cityForecasts, $type);

        self::assertEquals([], $response->getForecastsDay());
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
                CityWeatherForecast::fromArray(
                    [
                        'forecastsDay' => [
                            CityWeatherForecastDay::create('Sunny'),
                            CityWeatherForecastDay::create('Cloudy'),
                            CityWeatherForecastDay::create('Windy'),
                        ],
                    ]
                ),
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
                CityWeatherForecast::fromArray(
                    [
                        'forecastsDay' => [],
                    ]
                ),
            ],
        ];
    }
}
