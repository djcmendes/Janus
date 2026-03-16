<?php

declare(strict_types=1);

namespace App\Settings\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project-wide settings stored as a single row (singleton pattern).
 * Call Settings::getInstance() to retrieve or create the record.
 */
#[ORM\Entity]
#[ORM\Table(name: 'settings')]
class Settings
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $projectName = 'Janus';

    #[ORM\Column(length: 10)]
    private string $defaultLanguage = 'en-US';

    #[ORM\Column(length: 50)]
    private string $defaultAppearance = 'auto'; // 'light' | 'dark' | 'auto'

    #[ORM\Column(nullable: true)]
    private ?string $projectUrl = null;

    #[ORM\Column(nullable: true)]
    private ?string $projectLogo = null;

    #[ORM\Column(nullable: true)]
    private ?string $projectColor = null;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getProjectName(): string { return $this->projectName; }
    public function setProjectName(string $v): static { $this->projectName = $v; $this->touch(); return $this; }
    public function getDefaultLanguage(): string { return $this->defaultLanguage; }
    public function setDefaultLanguage(string $v): static { $this->defaultLanguage = $v; $this->touch(); return $this; }
    public function getDefaultAppearance(): string { return $this->defaultAppearance; }
    public function setDefaultAppearance(string $v): static { $this->defaultAppearance = $v; $this->touch(); return $this; }
    public function getProjectUrl(): ?string { return $this->projectUrl; }
    public function setProjectUrl(?string $v): static { $this->projectUrl = $v; $this->touch(); return $this; }
    public function getProjectLogo(): ?string { return $this->projectLogo; }
    public function setProjectLogo(?string $v): static { $this->projectLogo = $v; $this->touch(); return $this; }
    public function getProjectColor(): ?string { return $this->projectColor; }
    public function setProjectColor(?string $v): static { $this->projectColor = $v; $this->touch(); return $this; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    public function toArray(): array
    {
        return [
            'project_name'        => $this->projectName,
            'default_language'    => $this->defaultLanguage,
            'default_appearance'  => $this->defaultAppearance,
            'project_url'         => $this->projectUrl,
            'project_logo'        => $this->projectLogo,
            'project_color'       => $this->projectColor,
            'updated_at'          => $this->updatedAt->format(\DateTimeInterface::ATOM),
        ];
    }

    private function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }
}
