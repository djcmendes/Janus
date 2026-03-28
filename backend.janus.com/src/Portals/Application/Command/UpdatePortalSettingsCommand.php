<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;
final class UpdatePortalSettingsCommand
{
    public function __construct(
        public readonly string  $id,
        public readonly ?string $name      = null,
        public readonly ?string $baseRoute = null,
        public readonly ?string $status    = null,
        public readonly ?array  $settings  = null,
    ) {}
}
