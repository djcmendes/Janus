<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;

final class UpdateMagnetSourceCommand
{
    public function __construct(
        public readonly string  $magnetId,
        public readonly string  $sourceType,
        public readonly array   $sourceConfig,
        public readonly ?string $name     = null,
        public readonly ?string $schedule = null,
    ) {}
}
