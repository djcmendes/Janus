<?php

declare(strict_types=1);

namespace App\Settings\Presentation\DTO;

use App\Settings\Application\Command\UpdateSettingsCommand;

final class UpdateSettingsRequest
{
    private const ALLOWED_APPEARANCES = ['light', 'dark', 'auto'];

    private function __construct(
        public readonly ?string $projectName,
        public readonly ?string $defaultLanguage,
        public readonly ?string $defaultAppearance,
        public readonly mixed   $projectUrl,
        public readonly mixed   $projectLogo,
        public readonly mixed   $projectColor,
    ) {}

    /** @throws \InvalidArgumentException */
    public static function fromArray(array $data): self
    {
        if (isset($data['default_appearance'])
            && !in_array($data['default_appearance'], self::ALLOWED_APPEARANCES, true)
        ) {
            throw new \InvalidArgumentException(
                sprintf('default_appearance must be one of: %s.', implode(', ', self::ALLOWED_APPEARANCES))
            );
        }

        return new self(
            projectName:       isset($data['project_name'])       ? trim($data['project_name']) : null,
            defaultLanguage:   isset($data['default_language'])   ? trim($data['default_language']) : null,
            defaultAppearance: $data['default_appearance']        ?? null,
            // Use UNCHANGED sentinel so null means "clear the field" vs "not provided"
            projectUrl:        array_key_exists('project_url',   $data) ? $data['project_url']   : UpdateSettingsCommand::UNCHANGED,
            projectLogo:       array_key_exists('project_logo',  $data) ? $data['project_logo']  : UpdateSettingsCommand::UNCHANGED,
            projectColor:      array_key_exists('project_color', $data) ? $data['project_color'] : UpdateSettingsCommand::UNCHANGED,
        );
    }
}
