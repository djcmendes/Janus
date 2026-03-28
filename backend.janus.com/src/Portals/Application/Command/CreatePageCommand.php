<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;
final class CreatePageCommand
{
    public function __construct(
        public readonly string  $portalId,
        public readonly string  $title,
        public readonly string  $slug,
        public readonly ?string $parentId       = null,
        public readonly ?string $layoutTemplateId = null,
        public readonly array   $meta           = [],
        public readonly int     $sortOrder      = 0,
    ) {}
}
