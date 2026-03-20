<?php

declare(strict_types=1);

namespace App\Presets\Domain\Exception;

final class PresetNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct("Preset '{$id}' not found.");
    }
}
