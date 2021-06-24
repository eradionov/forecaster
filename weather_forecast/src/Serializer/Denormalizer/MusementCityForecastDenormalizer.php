<?php

declare(strict_types=1);

namespace App\Serializer\Denormalizer;

use App\DTO\CityWeatherForecast;
use App\DTO\CityWeatherForecastDay;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class MusementCityForecastDenormalizer implements DenormalizerInterface
{
    private ObjectNormalizer $normalizer;

    /**
     * ObjectNormalizer $normalizer
     */
    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $cityForecastDays = [
            'forecastsDay' => [],
        ];

        if (isset($data['forecast'], $data['forecast']['forecastday'])) {
            foreach ($data['forecast']['forecastday'] as $decodedResponse) {
                if (!isset($decodedResponse['day'], $decodedResponse['day']['condition'], $decodedResponse['day']['condition']['text'])) {
                    continue;
                }

                $cityForecastDays['forecastsDay'][] = $this->normalizer->denormalize(
                    [
                        'condition' => $decodedResponse['day']['condition']['text'],
                    ],
                    CityWeatherForecastDay::class,
                    $format,
                    $context
                );
            }
        }

        return $this->normalizer->denormalize($cityForecastDays, $type, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return CityWeatherForecast::class === $type;
    }
}
