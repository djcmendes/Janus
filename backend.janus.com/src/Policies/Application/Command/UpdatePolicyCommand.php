<?php

declare(strict_types=1);

namespace App\Policies\Application\Command;

final class UpdatePolicyCommand
{
    public const UNCHANGED = '__UNCHANGED__';

    public function __construct(
        public readonly string  $id,
        public readonly ?string $name        = null,
        public readonly mixed   $description = self::UNCHANGED,
        public readonly mixed   $icon        = self::UNCHANGED,
        public readonly ?bool   $enforceTfa  = null,
        public readonly ?bool   $adminAccess = null,
        public readonly ?bool   $appAccess   = null,
        public readonly mixed   $ipAccess    = self::UNCHANGED,
    ) {}
}
