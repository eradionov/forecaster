<?php

namespace App\Formatter;

use App\Exception\InvalidFormatException;

interface ArrayToStringFormatterInterface
{
    /**
     * @param array<string, mixed> $data
     *
     * @return string
     *
     * @throws InvalidFormatException if invalid array format passed
     */
    public function format(array $data): string;
}
