<?php

declare(strict_types=1);

namespace App\Tests\Application\Repository;

use App\Application\Handler\MusementCityApiHandler;
use App\Application\Handler\MusementCityForecastApiHandler;
use App\Application\Repository\ApiHandlerRepository;
use PHPUnit\Framework\TestCase;

final class ApiHandlerRepositoryTest extends TestCase
{
    public function testGetters(): void
    {
        $musementCityApiHandler = $this->createMock(MusementCityApiHandler::class);
        $musementCityForecastApiHandler = $this->createMock(MusementCityForecastApiHandler::class);

        $handlerRepository = new ApiHandlerRepository($musementCityApiHandler, $musementCityForecastApiHandler);

        self::assertTrue($musementCityApiHandler === $handlerRepository->getMusementCityApiHandler());
        self::assertTrue($musementCityForecastApiHandler === $handlerRepository->getMusementCityForecaseApiHandler());
    }
}
