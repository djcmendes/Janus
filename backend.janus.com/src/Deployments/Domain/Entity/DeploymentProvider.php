<?php

/**
 * @file DeploymentProvider.php
 *
 * Pure domain entity representing a configured deployment target. Zero framework dependencies.
 *
 * @package App\Deployments\Domain\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Entity;

use App\Deployments\Domain\Enum\DeploymentProviderType;
use Symfony\Component\Uid\Uuid;

/**
 * A configured deployment target — holds the URL and credentials needed to
 * trigger an external build/deploy process.
 *
 * The constructor generates a new UUIDv7 identity; use reconstitute() to reload
 * an existing record from persistence without creating a new ID.
 */
final class DeploymentProvider
{
    /** @var string UUIDv7 string primary key. */
    private string $id;

    /** @var string Human-readable provider name. */
    private string $name;

    /** @var DeploymentProviderType Integration type (webhook, netlify, vercel, custom). */
    private DeploymentProviderType $type;

    /** @var string Webhook or build-hook URL. */
    private string $url;

    /** @var array<string, mixed>|null Extra options (e.g. HTTP headers, site ID). */
    private ?array $options = null;

    /** @var bool Whether this provider is enabled for triggering. */
    private bool $isActive = true;

    /** @var \DateTimeImmutable Creation timestamp (UTC). */
    private \DateTimeImmutable $createdAt;

    /** @var \DateTimeImmutable|null Last-modification timestamp (UTC), or null when never edited. */
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Creates a new DeploymentProvider with a generated UUIDv7 identity.
     *
     * @param string                $name Provider display name.
     * @param DeploymentProviderType $type Integration type.
     * @param string                $url  Webhook or build-hook URL.
     */
    public function __construct(string $name, DeploymentProviderType $type, string $url)
    {
        $this->id        = (string) Uuid::v7();
        $this->name      = $name;
        $this->type      = $type;
        $this->url       = $url;
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Reconstitutes an existing DeploymentProvider from persisted data without generating a new ID.
     *
     * @param string                        $id        Existing UUIDv7 string.
     * @param string                        $name      Provider display name.
     * @param DeploymentProviderType        $type      Integration type.
     * @param string                        $url       Webhook or build-hook URL.
     * @param array<string, mixed>|null     $options   Extra options, or null.
     * @param bool                          $isActive  Whether the provider is active.
     * @param \DateTimeImmutable            $createdAt Original creation timestamp.
     * @param \DateTimeImmutable|null       $updatedAt Last-modification timestamp, or null.
     *
     * @return self A DeploymentProvider instance reflecting the persisted state.
     */
    public static function reconstitute(
        string                  $id,
        string                  $name,
        DeploymentProviderType  $type,
        string                  $url,
        ?array                  $options,
        bool                    $isActive,
        \DateTimeImmutable      $createdAt,
        ?\DateTimeImmutable     $updatedAt,
    ): self {
        $self            = new self($name, $type, $url);
        $self->id        = $id;
        $self->options   = $options;
        $self->isActive  = $isActive;
        $self->createdAt = $createdAt;
        $self->updatedAt = $updatedAt;

        return $self;
    }

    /**
     * Returns the UUIDv7 string primary key.
     *
     * @return string UUID string.
     */
    public function getId(): string { return $this->id; }

    /**
     * Returns the human-readable provider name.
     *
     * @return string Provider name.
     */
    public function getName(): string { return $this->name; }

    /**
     * Updates the provider name and refreshes the updatedAt timestamp.
     *
     * @param  string $name New display name.
     * @return static        Fluent self for chaining.
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this->touch();
    }

    /**
     * Returns the integration type.
     *
     * @return DeploymentProviderType Enum case.
     */
    public function getType(): DeploymentProviderType { return $this->type; }

    /**
     * Returns the webhook or build-hook URL.
     *
     * @return string The target URL.
     */
    public function getUrl(): string { return $this->url; }

    /**
     * Updates the webhook URL and refreshes the updatedAt timestamp.
     *
     * @param  string $url New target URL.
     * @return static       Fluent self for chaining.
     */
    public function setUrl(string $url): static
    {
        $this->url = $url;
        return $this->touch();
    }

    /**
     * Returns the extra options array, or null when not configured.
     *
     * @return array<string, mixed>|null Options map, or null.
     */
    public function getOptions(): ?array { return $this->options; }

    /**
     * Updates the extra options and refreshes the updatedAt timestamp.
     *
     * @param  array<string, mixed>|null $options New options map, or null.
     * @return static                              Fluent self for chaining.
     */
    public function setOptions(?array $options): static
    {
        $this->options = $options;
        return $this->touch();
    }

    /**
     * Returns whether this provider is enabled for triggering.
     *
     * @return bool True when active.
     */
    public function isActive(): bool { return $this->isActive; }

    /**
     * Sets the active flag and refreshes the updatedAt timestamp.
     *
     * @param  bool   $isActive New active state.
     * @return static            Fluent self for chaining.
     */
    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this->touch();
    }

    /**
     * Returns the creation timestamp.
     *
     * @return \DateTimeImmutable Immutable UTC creation timestamp.
     */
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    /**
     * Returns the last-modification timestamp, or null when never edited.
     *
     * @return \DateTimeImmutable|null Immutable UTC last-modification timestamp, or null.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    /**
     * Refreshes the updatedAt timestamp to the current time.
     *
     * @return static Fluent self for chaining.
     */
    private function touch(): static
    {
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
