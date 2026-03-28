<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;
final class CreatePortalCommand
{
    public function __construct(
        public readonly string  $name,
        public readonly string  $baseRoute,
        public readonly string  $status   = 'draft',
        public readonly array   $settings = [],
    ) {}
}
