<?php

declare(strict_types=1);

namespace App\Tests\Formatter;

use App\DTO\CityWeatherForecast;
use App\DTO\CityWeatherForecastDay;
use App\DTO\MusementCity;
use App\Formatter\ForecastFormatter;
use PHPUnit\Framework\TestCase;

final class ForecastFormatterTest extends TestCase
{
    /**
     * @param array<int, array{name: string, latitude: float, longitude: float, forecast: CityWeatherForecast}> $musementCities
     * @param array<int, string> $results
     *
     * @dataProvider musementCityProvider
     */
    public function testFormatterWithTwoDaysResponse(array $musementCities, array $results): void
    {
        foreach ($musementCities as $key => $city) {
            $musementCity = MusementCity::fromArray($city);
            $forecastFormatter = new ForecastFormatter();

            self::assertSame(
                $results[$key],
                $forecastFormatter->format($musementCity)
            );
        }
    }

    /**
     * @return array<int, array>
     */
    public function musementCityProvider(): array
    {
        return [
            [
                [
                    [
                        'name' => 'Amsterdam',
                        'latitude' => 43.16,
                        'longitude' => 23.12,
                        'forecast' => CityWeatherForecast::fromArray(
                            [
                                'forecastsDay' => [
                                    CityWeatherForecastDay::create('Sunny'),
                                    CityWeatherForecastDay::create('Cloudy'),
                                ],
                            ]
                        ),
                    ],
                    [
                        'name' => 'London',
                        'latitude' => 43.16,
                        'longitude' => 23.12,
                        'forecast' => CityWeatherForecast::fromArray(
                            [
                                'forecastsDay' => [
                                    CityWeatherForecastDay::create('Snowy'),
                                    CityWeatherForecastDay::create('Rainy'),
                                    CityWeatherForecastDay::create('Cloudy'),
                                ],
                            ]
                        ),
                    ],
                    [
                        'name' => 'Minsk',
                        'latitude' => 43.16,
                        'longitude' => 23.12,
                        'forecast' => CityWeatherForecast::fromArray(['forecastsDay' => []]),
                    ],
                ],
                [
                    'Processed city Amsterdam | Sunny - Cloudy',
                    'Processed city London | Snowy - Rainy - Cloudy',
                    'Processed city Minsk | ',
                ],
            ],
        ];
    }
}
