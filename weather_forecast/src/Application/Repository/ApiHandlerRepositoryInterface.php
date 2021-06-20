<?php

declare(strict_types=1);

namespace App\Application\Repository;

use App\Application\Handler\MusementCityApiHandler;
use App\Application\Handler\MusementCityForecastApiHandler;

interface ApiHandlerRepositoryInterface
{
    /**
     * @return MusementCityApiHandler
     */
    public function getMusementCityApiHandler(): MusementCityApiHandler;

    /**
     * @return MusementCityForecastApiHandler
     */
    public function getMusementCityForecaseApiHandler(): MusementCityForecastApiHandler;
}
