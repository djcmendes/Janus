<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;
final class UpdateComponentCommand
{
    public function __construct(
        public readonly string  $id,
        public readonly ?string $collectionId  = null,
        public readonly array   $queryConfig   = [],
        public readonly array   $renderConfig  = [],
    ) {}
}
