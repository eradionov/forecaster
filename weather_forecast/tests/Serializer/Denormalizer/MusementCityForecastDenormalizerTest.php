<?php

declare(strict_types=1);

namespace App\Tests\Serializer\Denormalizer;

use App\DTO\CityWeatherForecast;
use App\Serializer\Denormalizer\MusementCityForecastDenormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class MusementCityForecastDenormalizerTest extends TestCase
{
    /**
     * @dataProvider getResponseWithForecasts
     *
     * @param array<int, array> $cityForecasts
     * @param string $city
     * @param array<int, string> $forecastsResponseSample
     */
    public function testWithForecasts(array $cityForecasts, string $city, array $forecastsResponseSample): void
    {
        $type = CityWeatherForecast::class;
        $response = (new MusementCityForecastDenormalizer(new ObjectNormalizer()))->denormalize($cityForecasts, $type);

        self::assertEquals($forecastsResponseSample, $response->getForecasts());
        self::assertEquals($city, $response->getCity());
    }

    /**
     * @dataProvider getResponseWithoutForecasts
     *
     * @param array<int, array> $cityForecasts
     * @param string $city
     */
    public function testWithoutForecasts(array $cityForecasts, string $city): void
    {
        $type = CityWeatherForecast::class;
        $response = (new MusementCityForecastDenormalizer(new ObjectNormalizer()))->denormalize($cityForecasts, $type);

        self::assertEquals([], $response->getForecasts());
        self::assertEquals($city, $response->getCity());
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
                'New-York',
                ['Sunny', 'Cloudy', 'Windy'],
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
                'New-York',
            ],
        ];
    }
}
