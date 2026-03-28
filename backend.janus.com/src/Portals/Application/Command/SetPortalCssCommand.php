<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;

final class SetPortalCssCommand
{
    public function __construct(
        public readonly string  $portalId,
        public readonly ?string $css,
    ) {}
}
