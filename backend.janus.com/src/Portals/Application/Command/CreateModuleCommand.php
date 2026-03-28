<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;
final class CreateModuleCommand
{
    public function __construct(
        public readonly string  $type,
        public readonly string  $name,
        public readonly array   $config   = [],
        public readonly ?string $portalId = null,
    ) {}
}
