<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;
final class PlaceModuleCommand
{
    public function __construct(
        public readonly string $pageId,
        public readonly string $positionName,
        public readonly string $moduleId,
        public readonly int    $sortOrder = 0,
    ) {}
}
