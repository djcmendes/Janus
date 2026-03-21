<?php

declare(strict_types=1);

namespace App\Roles\Application\Command;

final class UpdateRoleCommand
{
    public function __construct(
        public readonly string  $id,
        public readonly ?string $name         = null,
        public readonly mixed   $description  = UpdateRoleCommand::UNCHANGED,
        public readonly mixed   $icon         = UpdateRoleCommand::UNCHANGED,
        public readonly ?bool   $enforceTfa   = null,
        public readonly ?bool   $adminAccess  = null,
        public readonly ?bool   $appAccess    = null,
    ) {}

    public const UNCHANGED = '__UNCHANGED__';
}
