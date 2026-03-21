<?php

declare(strict_types=1);

namespace App\Policies\Application\Command;

final class CreatePolicyCommand
{
    public function __construct(
        public readonly string  $name,
        public readonly ?string $description = null,
        public readonly ?string $icon        = null,
        public readonly bool    $enforceTfa  = false,
        public readonly bool    $adminAccess = false,
        public readonly bool    $appAccess   = true,
        public readonly ?array  $ipAccess    = null,
    ) {}
}
