<?php

declare(strict_types=1);

namespace App\Policies\Application\Command;

final class CreateAccessCommand
{
    public function __construct(
        public readonly string  $policyId,
        public readonly ?string $roleId = null, // null = public access level
    ) {}
}
