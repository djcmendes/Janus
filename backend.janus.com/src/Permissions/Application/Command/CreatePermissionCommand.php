<?php

declare(strict_types=1);

namespace App\Permissions\Application\Command;

final class CreatePermissionCommand
{
    public function __construct(
        public readonly string  $policyId,
        public readonly string  $action,
        public readonly ?string $collection        = null,
        public readonly ?array  $fields            = null,
        public readonly ?array  $permissionsFilter = null,
        public readonly ?array  $validation        = null,
        public readonly ?array  $presets           = null,
    ) {}
}
