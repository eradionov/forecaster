<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use Symfony\Component\Serializer\SerializerInterface;

interface FactoryInterface
{
    /**
     * @return SerializerInterface
     */
    public static function build(): SerializerInterface;
}
