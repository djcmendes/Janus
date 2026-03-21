<?php

declare(strict_types=1);

namespace App\Permissions\Application\Command;

final class UpdatePermissionCommand
{
    public const UNCHANGED = '__UNCHANGED__';

    public function __construct(
        public readonly string  $id,
        public readonly ?string $action            = null,
        public readonly mixed   $collection        = self::UNCHANGED,
        public readonly mixed   $fields            = self::UNCHANGED,
        public readonly mixed   $permissionsFilter = self::UNCHANGED,
        public readonly mixed   $validation        = self::UNCHANGED,
        public readonly mixed   $presets           = self::UNCHANGED,
    ) {}
}
