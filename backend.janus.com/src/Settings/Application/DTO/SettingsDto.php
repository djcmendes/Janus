<?php

declare(strict_types=1);

namespace App\Settings\Application\DTO;

use App\Settings\Domain\Entity\Settings;

final class SettingsDto
{
    public function __construct(
        public readonly string  $projectName,
        public readonly string  $defaultLanguage,
        public readonly string  $defaultAppearance,
        public readonly ?string $projectUrl,
        public readonly ?string $projectLogo,
        public readonly ?string $projectColor,
        public readonly string  $updatedAt,
    ) {}

    public static function fromEntity(Settings $settings): self
    {
        return new self(
            projectName:       $settings->getProjectName(),
            defaultLanguage:   $settings->getDefaultLanguage(),
            defaultAppearance: $settings->getDefaultAppearance(),
            projectUrl:        $settings->getProjectUrl(),
            projectLogo:       $settings->getProjectLogo(),
            projectColor:      $settings->getProjectColor(),
            updatedAt:         $settings->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'project_name'       => $this->projectName,
            'default_language'   => $this->defaultLanguage,
            'default_appearance' => $this->defaultAppearance,
            'project_url'        => $this->projectUrl,
            'project_logo'       => $this->projectLogo,
            'project_color'      => $this->projectColor,
            'updated_at'         => $this->updatedAt,
        ];
    }
}
