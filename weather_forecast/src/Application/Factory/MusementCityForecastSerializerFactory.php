<?php

declare(strict_types=1);

namespace App\Application\Factory;

use App\Application\Serializer\Normalizer\MusementCityForecastDenormalizer;
use App\Factory\FactoryInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class MusementCityForecastSerializerFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public static function build(): SerializerInterface
    {
        return new Serializer([
            new ArrayDenormalizer(),
            new MusementCityForecastDenormalizer(),
            new DateTimeNormalizer(),
        ], [new JsonEncoder()]);
    }
}
