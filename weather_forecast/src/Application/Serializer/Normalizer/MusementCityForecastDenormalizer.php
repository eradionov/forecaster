<?php

declare(strict_types=1);

namespace App\Application\Serializer\Normalizer;

use App\Application\DTO\CityWeatherForecast;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class MusementCityForecastDenormalizer implements DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $cityForecastDays = [];

        if (isset($data['forecast']) && isset($data['forecast']['forecastday'])) {
            foreach ($data['forecast']['forecastday'] as $decodedResponse) {
                $cityForecastDays[] = $decodedResponse['day']['condition']['text'];
            }
        }

        $cityForecast = new CityWeatherForecast();
        $cityForecast->setCity($data['location']['name'] ?? '');
        $cityForecast->setCityForecastDays($cityForecastDays);

        return $cityForecast;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $type === CityWeatherForecast::class;
    }
}
