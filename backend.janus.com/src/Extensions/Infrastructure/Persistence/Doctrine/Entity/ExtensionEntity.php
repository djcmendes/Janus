<?php

/**
 * @file ExtensionEntity.php
 *
 * Doctrine ORM persistence model for the `extensions` table.
 * This class is the sole owner of all database-mapping concerns for extension records.
 * Domain logic lives exclusively in Extension (Domain\Entity).
 *
 * @package App\Extensions\Infrastructure\Persistence\Doctrine\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Infrastructure\Persistence\Doctrine\Entity;

use App\Extensions\Domain\Enum\ExtensionType;
use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine entity mapping extension records to the `extensions` table.
 *
 * Non-final to allow Doctrine proxy subclass generation. All persistence
 * concerns are confined here; the domain Extension class has no framework ties.
 */
#[ORM\Entity]
#[ORM\Table(name: 'extensions')]
#[ORM\UniqueConstraint(name: 'uniq_extensions_name_type', columns: ['name', 'type'])]
class ExtensionEntity
{
    /** @var string UUIDv7 string primary key. */
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    /** @var string Package/bundle name — unique per type. */
    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    /** @var ExtensionType Integration kind. */
    #[ORM\Column(type: 'string', length: 16, enumType: ExtensionType::class)]
    private ExtensionType $type;

    /** @var string Semantic version string. */
    #[ORM\Column(type: 'string', length: 64)]
    private string $version;

    /** @var bool Whether the extension is currently active. */
    #[ORM\Column(type: 'boolean')]
    private bool $enabled;

    /** @var string|null Optional human-readable description. */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    /** @var array<string, mixed>|null Entry-point configuration (JSON). */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $meta;

    /** @var \DateTimeImmutable Creation timestamp (UTC). */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /** @var \DateTimeImmutable Last-modification timestamp (UTC). */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    /**
     * Returns the UUIDv7 string primary key.
     *
     * @return string UUID string.
     */
    public function getId(): string { return $this->id; }

    /**
     * @param string $id UUID string to assign as primary key.
     * @return static
     */
    public function setId(string $id): static { $this->id = $id; return $this; }

    /**
     * Returns the package/bundle name.
     *
     * @return string Extension name.
     */
    public function getName(): string { return $this->name; }

    /**
     * @param string $name Package/bundle name.
     * @return static
     */
    public function setName(string $name): static { $this->name = $name; return $this; }

    /**
     * Returns the integration kind.
     *
     * @return ExtensionType Enum case.
     */
    public function getType(): ExtensionType { return $this->type; }

    /**
     * @param ExtensionType $type Integration kind.
     * @return static
     */
    public function setType(ExtensionType $type): static { $this->type = $type; return $this; }

    /**
     * Returns the semantic version string.
     *
     * @return string Version string.
     */
    public function getVersion(): string { return $this->version; }

    /**
     * @param string $version Semantic version string.
     * @return static
     */
    public function setVersion(string $version): static { $this->version = $version; return $this; }

    /**
     * Returns whether the extension is currently active.
     *
     * @return bool True when enabled.
     */
    public function isEnabled(): bool { return $this->enabled; }

    /**
     * @param bool $enabled Active state.
     * @return static
     */
    public function setEnabled(bool $enabled): static { $this->enabled = $enabled; return $this; }

    /**
     * Returns the human-readable description, or null when not set.
     *
     * @return string|null Description text, or null.
     */
    public function getDescription(): ?string { return $this->description; }

    /**
     * @param string|null $description Description text, or null.
     * @return static
     */
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    /**
     * Returns the entry-point configuration array, or null when not configured.
     *
     * @return array<string, mixed>|null Options map, or null.
     */
    public function getMeta(): ?array { return $this->meta; }

    /**
     * @param array<string, mixed>|null $meta Options map, or null.
     * @return static
     */
    public function setMeta(?array $meta): static { $this->meta = $meta; return $this; }

    /**
     * Returns the creation timestamp.
     *
     * @return \DateTimeImmutable Immutable UTC creation timestamp.
     */
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    /**
     * @param \DateTimeImmutable $createdAt Creation timestamp.
     * @return static
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    /**
     * Returns the last-modification timestamp.
     *
     * @return \DateTimeImmutable Immutable UTC last-modification timestamp.
     */
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    /**
     * @param \DateTimeImmutable $updatedAt Last-modification timestamp.
     * @return static
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }
}
