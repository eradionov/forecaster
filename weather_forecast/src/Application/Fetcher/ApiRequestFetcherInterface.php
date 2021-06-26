<?php

declare(strict_types=1);

namespace App\Application\Fetcher;

use App\Utils\RequestParams;

interface ApiRequestFetcherInterface
{
    /**
     * @param RequestParams|null $requestParams
     *
     * @return mixed
     */
    public function fetch(RequestParams $requestParams = null);
}
