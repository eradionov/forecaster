<?php

declare(strict_types=1);

namespace App\Exception;

final class MusementCityProcessingException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Some errors occurred during processing, please see log for details.');
    }
}
