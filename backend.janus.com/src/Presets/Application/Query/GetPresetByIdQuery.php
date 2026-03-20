<?php

declare(strict_types=1);

namespace App\Presets\Application\Query;

final class GetPresetByIdQuery
{
    public function __construct(public readonly string $id) {}
}
