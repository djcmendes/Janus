<?php

declare(strict_types=1);

namespace App\Settings\Domain\Entity;

use DateTimeImmutable;
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
    private DateTimeImmutable $updatedAt;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getProjectName(): string
    {
        return $this->projectName;
    }

    /**
     *
     * @return self
     */
    public function setProjectName(string $name): static
    {
        $this->projectName = $name;
        $this->touch();
        return $this;
    }

    public function getDefaultLanguage(): string
    {
        return $this->defaultLanguage;
    }

    public function setDefaultLanguage(string $lang): static
    {
        $this->defaultLanguage = $lang;
        $this->touch();
        return $this;
    }

    public function getDefaultAppearance(): string
    {
        return $this->defaultAppearance;
    }

    public function setDefaultAppearance(string $appearance): static
    {
        $this->defaultAppearance = $appearance;
        $this->touch();
        return $this;
    }

    public function getProjectUrl(): ?string
    {
        return $this->projectUrl;
    }

    public function setProjectUrl(?string $v): static
    {
        $this->projectUrl = $v;
        $this->touch();
        return $this;
    }

    public function getProjectLogo(): ?string { return $this->projectLogo; }
    public function setProjectLogo(?string $v): static { $this->projectLogo = $v; $this->touch(); return $this; }
    public function getProjectColor(): ?string { return $this->projectColor; }
    public function setProjectColor(?string $v): static { $this->projectColor = $v; $this->touch(); return $this; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    private function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }
}
