<?php

declare(strict_types=1);

namespace App\Settings\Application\Command;

final class UpdateSettingsCommand
{
    public function __construct(
        public readonly ?string $projectName       = null,
        public readonly ?string $defaultLanguage   = null,
        public readonly ?string $defaultAppearance = null,
        // Nullable with a sentinel so we can distinguish "not sent" from "explicitly set to null"
        public readonly mixed   $projectUrl        = UpdateSettingsCommand::UNCHANGED,
        public readonly mixed   $projectLogo       = UpdateSettingsCommand::UNCHANGED,
        public readonly mixed   $projectColor      = UpdateSettingsCommand::UNCHANGED,
    ) {}

    public const UNCHANGED = '__UNCHANGED__';
}
