<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;
final class UpdateModuleConfigCommand
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly array  $config = [],
    ) {}
}
