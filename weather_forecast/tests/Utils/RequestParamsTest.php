<?php

declare(strict_types=1);

namespace App\Tests\Utils;

use App\Utils\RequestParams;
use PHPUnit\Framework\TestCase;

final class RequestParamsTest extends TestCase
{
    public function testRequestWithData(): void
    {
        $query = ['q' => 'api', 'item2' => 'test'];
        $headers = ['Content-Type' => 'application/json'];

        $requestParams = RequestParams::create($query, $headers);

        self::assertEquals($requestParams->toArray(), [
            'query' => $query,
            'headers' => $headers,
        ]);
    }
}
