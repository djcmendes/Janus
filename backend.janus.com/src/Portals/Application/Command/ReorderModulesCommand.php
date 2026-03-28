<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;
final class ReorderModulesCommand
{
    /** @param array<array{id: string, sort_order: int}> $orderedItems */
    public function __construct(
        public readonly string $pageId,
        public readonly array  $orderedItems,
    ) {}
}
