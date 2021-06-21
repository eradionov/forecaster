<?php

declare(strict_types=1);

namespace App\Tests\Application\Factory;

use App\Application\Factory\MusementCityForecastSerializerFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

final class MusementCityForecastSerializerFactoryTest extends TestCase
{
    public function testReturnedInstance(): void
    {
        self::assertInstanceOf(SerializerInterface::class, MusementCityForecastSerializerFactory::build());
    }
}
