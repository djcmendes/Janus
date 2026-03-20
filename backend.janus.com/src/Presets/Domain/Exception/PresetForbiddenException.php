<?php

declare(strict_types=1);

namespace App\Presets\Domain\Exception;

final class PresetForbiddenException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('You do not have permission to modify this preset.');
    }
}
