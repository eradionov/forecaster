<?php

declare(strict_types=1);

namespace App\Application\Handler;

use App\Utils\RequestParams;

interface ApiRequestHandlerInterface
{
    /**
     * @param RequestParams|null $requestParams
     *
     * @return mixed
     */
    public function fetch(RequestParams $requestParams = null);
}
