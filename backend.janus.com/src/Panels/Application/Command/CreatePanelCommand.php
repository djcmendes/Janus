<?php

declare(strict_types=1);

namespace App\Panels\Application\Command;

final class CreatePanelCommand
{
    public function __construct(
        public readonly string  $dashboardId,
        public readonly string  $type,
        public readonly ?string $name      = null,
        public readonly ?string $note      = null,
        public readonly ?array  $options   = null,
        public readonly int     $positionX = 0,
        public readonly int     $positionY = 0,
        public readonly int     $width     = 6,
        public readonly int     $height    = 4,
    ) {}
}
