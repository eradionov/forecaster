<?php

declare(strict_types=1);

namespace App\Formatter;

use App\Exception\InvalidFormatException;

final class ForecastFormatter implements ArrayToStringFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function format(array $data): string
    {
        if (!\array_key_exists('city', $data) || !\array_key_exists('forecasts', $data)) {
            throw new InvalidFormatException('Invalid format passed.\'city, forecasts\' should present.');
        }

        return sprintf(
            'Processed city %s | %s',
            $data['city'],
            implode(' - ', $data['forecasts'])
        );
    }
}
