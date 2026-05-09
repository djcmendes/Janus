<?php

/**
 * @file Extension.php
 *
 * Pure domain entity representing a registered extension. Zero framework dependencies.
 *
 * @package App\Extensions\Domain\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Domain\Entity;

use App\Extensions\Domain\Enum\ExtensionType;
use Symfony\Component\Uid\Uuid;

/**
 * A registered extension entry in the Janus extension registry.
 *
 * The constructor generates a new UUIDv7 identity; use reconstitute() to reload
 * an existing record from persistence without creating a new ID or resetting timestamps.
 */
final class Extension
{
    /** @var string UUIDv7 string primary key. */
    private string $id;

    /** @var string Package/bundle name — unique per type. */
    private string $name;

    /** @var ExtensionType Integration kind. */
    private ExtensionType $type;

    /** @var string Semantic version string (e.g. "1.0.0"). */
    private string $version;

    /** @var bool Whether the extension is currently active. */
    private bool $enabled;

    /** @var string|null Optional human-readable description. */
    private ?string $description;

    /** @var array<string, mixed>|null Entry-point configuration (JSON). */
    private ?array $meta;

    /** @var \DateTimeImmutable Creation timestamp (UTC). */
    private \DateTimeImmutable $createdAt;

    /** @var \DateTimeImmutable Last-modification timestamp (UTC). */
    private \DateTimeImmutable $updatedAt;

    /**
     * Creates a new Extension with a generated UUIDv7 identity.
     *
     * @param string                    $name        Package/bundle name.
     * @param ExtensionType             $type        Integration kind.
     * @param string                    $version     Semantic version string.
     * @param bool                      $enabled     Whether the extension is active (default false).
     * @param string|null               $description Optional human-readable description.
     * @param array<string, mixed>|null $meta        Entry-point configuration, or null.
     */
    public function __construct(
        string        $name,
        ExtensionType $type,
        string        $version,
        bool          $enabled     = false,
        ?string       $description = null,
        ?array        $meta        = null,
    ) {
        $this->id          = Uuid::v7()->toRfc4122();
        $this->name        = $name;
        $this->type        = $type;
        $this->version     = $version;
        $this->enabled     = $enabled;
        $this->description = $description;
        $this->meta        = $meta;
        $this->createdAt   = new \DateTimeImmutable();
        $this->updatedAt   = new \DateTimeImmutable();
    }

    /**
     * Reconstitutes an existing Extension from persisted data without generating a new ID.
     *
     * @param string                    $id          Existing UUIDv7 string.
     * @param string                    $name        Package/bundle name.
     * @param ExtensionType             $type        Integration kind.
     * @param string                    $version     Semantic version string.
     * @param bool                      $enabled     Whether the extension is active.
     * @param string|null               $description Optional human-readable description.
     * @param array<string, mixed>|null $meta        Entry-point configuration, or null.
     * @param \DateTimeImmutable        $createdAt   Original creation timestamp.
     * @param \DateTimeImmutable        $updatedAt   Last-modification timestamp.
     *
     * @return self An Extension instance reflecting the persisted state.
     */
    public static function reconstitute(
        string              $id,
        string              $name,
        ExtensionType       $type,
        string              $version,
        bool                $enabled,
        ?string             $description,
        ?array              $meta,
        \DateTimeImmutable  $createdAt,
        \DateTimeImmutable  $updatedAt,
    ): self {
        $self              = new self($name, $type, $version, $enabled, $description, $meta);
        $self->id          = $id;
        $self->createdAt   = $createdAt;
        $self->updatedAt   = $updatedAt;

        return $self;
    }

    /**
     * Returns the UUIDv7 string primary key.
     *
     * @return string UUID string.
     */
    public function getId(): string { return $this->id; }

    /**
     * Returns the package/bundle name.
     *
     * @return string Extension name.
     */
    public function getName(): string { return $this->name; }

    /**
     * Returns the integration kind.
     *
     * @return ExtensionType Enum case.
     */
    public function getType(): ExtensionType { return $this->type; }

    /**
     * Returns the semantic version string.
     *
     * @return string Version string (e.g. "1.0.0").
     */
    public function getVersion(): string { return $this->version; }

    /**
     * Returns whether the extension is currently active.
     *
     * @return bool True when enabled.
     */
    public function isEnabled(): bool { return $this->enabled; }

    /**
     * Returns the human-readable description, or null when not set.
     *
     * @return string|null Description text, or null.
     */
    public function getDescription(): ?string { return $this->description; }

    /**
     * Returns the entry-point configuration array, or null when not configured.
     *
     * @return array<string, mixed>|null Options map, or null.
     */
    public function getMeta(): ?array { return $this->meta; }

    /**
     * Returns the creation timestamp.
     *
     * @return \DateTimeImmutable Immutable UTC creation timestamp.
     */
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    /**
     * Returns the last-modification timestamp.
     *
     * @return \DateTimeImmutable Immutable UTC last-modification timestamp.
     */
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    /**
     * Sets the active flag and refreshes the updatedAt timestamp.
     *
     * @param  bool   $enabled New active state.
     * @return static           Fluent self for chaining.
     */
    public function setEnabled(bool $enabled): static
    {
        $this->enabled   = $enabled;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Sets the version string and refreshes the updatedAt timestamp.
     *
     * @param  string $version New semantic version string.
     * @return static           Fluent self for chaining.
     */
    public function setVersion(string $version): static
    {
        $this->version   = $version;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Sets the entry-point configuration and refreshes the updatedAt timestamp.
     *
     * @param  array<string, mixed>|null $meta New options map, or null.
     * @return static                           Fluent self for chaining.
     */
    public function setMeta(?array $meta): static
    {
        $this->meta      = $meta;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
