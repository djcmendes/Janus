<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;
final class CreateComponentCommand
{
    public function __construct(
        public readonly string  $type,
        public readonly ?string $collectionId  = null,
        public readonly array   $queryConfig   = [],
        public readonly array   $renderConfig  = [],
    ) {}
}
