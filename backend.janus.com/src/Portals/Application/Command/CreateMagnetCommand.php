<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;

final class CreateMagnetCommand
{
    public function __construct(
        public readonly string  $portalId,
        public readonly string  $name,
        public readonly string  $sourceType,
        public readonly string  $targetCollectionId,
        public readonly ?string $schedule = null,
    ) {}
}
