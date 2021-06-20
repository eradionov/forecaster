<?php

declare(strict_types=1);

namespace App\Application\Repository;

use App\Application\Handler\MusementCityApiHandler;
use App\Application\Handler\MusementCityForecastApiHandler;

final class ApiHandlerRepository implements ApiHandlerRepositoryInterface
{
    private MusementCityApiHandler $musementCityApiHandler;
    private MusementCityForecastApiHandler $musementCityForecastApiHandler;

    /**
     * @param MusementCityApiHandler $musementCityApiHandler
     * @param MusementCityForecastApiHandler $musementCityForecastApiHandler
     */
    public function __construct(
        MusementCityApiHandler $musementCityApiHandler,
        MusementCityForecastApiHandler $musementCityForecastApiHandler
    ) {
        $this->musementCityApiHandler = $musementCityApiHandler;
        $this->musementCityForecastApiHandler = $musementCityForecastApiHandler;
    }

    /**
     * @return MusementCityApiHandler
     */
    public function getMusementCityApiHandler(): MusementCityApiHandler
    {
        return $this->musementCityApiHandler;
    }

    /**
     * @return MusementCityForecastApiHandler
     */
    public function getMusementCityForecaseApiHandler(): MusementCityForecastApiHandler
    {
        return $this->musementCityForecastApiHandler;
    }
}
