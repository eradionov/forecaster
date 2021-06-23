<?php

declare(strict_types=1);

namespace App\Factory;

use App\Serializer\Denormalizer\MusementCityForecastDenormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class MusementApiSerializerFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public static function build(): SerializerInterface
    {
        return new Serializer([
            new ArrayDenormalizer(),
            new MusementCityForecastDenormalizer(new ObjectNormalizer()),
            new ObjectNormalizer(),
        ], [new JsonEncoder()]);
    }
}
