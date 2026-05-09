<?php

/**
 * @file DeploymentProviderEntity.php
 *
 * Doctrine ORM persistence model for the `deployment_providers` table.
 * This class is the sole owner of all database-mapping concerns for provider records.
 * Domain logic lives exclusively in DeploymentProvider (Domain\Entity).
 *
 * @package App\Deployments\Infrastructure\Persistence\Doctrine\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Persistence\Doctrine\Entity;

use App\Deployments\Domain\Enum\DeploymentProviderType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Doctrine entity mapping deployment provider records to the `deployment_providers` table.
 *
 * Non-final to allow Doctrine proxy subclass generation. All persistence
 * concerns are confined here; the domain DeploymentProvider class has no framework ties.
 */
#[ORM\Entity]
#[ORM\Table(name: 'deployment_providers')]
class DeploymentProviderEntity
{
    /** @var Uuid UUID primary key (stored as uuid column type). */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    /** @var string Human-readable provider name. */
    #[ORM\Column(length: 255)]
    private string $name;

    /** @var DeploymentProviderType Integration type. */
    #[ORM\Column(length: 20, enumType: DeploymentProviderType::class)]
    private DeploymentProviderType $type;

    /** @var string Webhook or build-hook URL. */
    #[ORM\Column(type: 'text')]
    private string $url;

    /** @var array<string, mixed>|null Extra options (HTTP headers, site ID, etc.). */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $options = null;

    /** @var bool Whether this provider is enabled for triggering. */
    #[ORM\Column]
    private bool $isActive = true;

    /** @var \DateTimeImmutable Creation timestamp (UTC). */
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    /** @var \DateTimeImmutable|null Last-modification timestamp (UTC). */
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Returns the UUID primary key.
     *
     * @return Uuid Doctrine-managed UUID value object.
     */
    public function getId(): Uuid { return $this->id; }

    /**
     * Sets the UUID primary key.
     *
     * @param  Uuid   $id UUID value object.
     * @return static      Fluent self for chaining.
     */
    public function setId(Uuid $id): static { $this->id = $id; return $this; }

    /**
     * Returns the human-readable provider name.
     *
     * @return string Provider name.
     */
    public function getName(): string { return $this->name; }

    /**
     * Sets the provider name.
     *
     * @param  string $name New name.
     * @return static        Fluent self for chaining.
     */
    public function setName(string $name): static { $this->name = $name; return $this; }

    /**
     * Returns the integration type.
     *
     * @return DeploymentProviderType Enum case.
     */
    public function getType(): DeploymentProviderType { return $this->type; }

    /**
     * Sets the integration type.
     *
     * @param  DeploymentProviderType $type Enum case.
     * @return static                        Fluent self for chaining.
     */
    public function setType(DeploymentProviderType $type): static { $this->type = $type; return $this; }

    /**
     * Returns the webhook or build-hook URL.
     *
     * @return string Target URL.
     */
    public function getUrl(): string { return $this->url; }

    /**
     * Sets the webhook URL.
     *
     * @param  string $url New target URL.
     * @return static       Fluent self for chaining.
     */
    public function setUrl(string $url): static { $this->url = $url; return $this; }

    /**
     * Returns the extra options array, or null.
     *
     * @return array<string, mixed>|null Options map, or null.
     */
    public function getOptions(): ?array { return $this->options; }

    /**
     * Sets the extra options array.
     *
     * @param  array<string, mixed>|null $options Options map, or null.
     * @return static                              Fluent self for chaining.
     */
    public function setOptions(?array $options): static { $this->options = $options; return $this; }

    /**
     * Returns whether this provider is enabled.
     *
     * @return bool True when active.
     */
    public function isActive(): bool { return $this->isActive; }

    /**
     * Sets the active flag.
     *
     * @param  bool   $isActive New active state.
     * @return static            Fluent self for chaining.
     */
    public function setIsActive(bool $isActive): static { $this->isActive = $isActive; return $this; }

    /**
     * Returns the creation timestamp.
     *
     * @return \DateTimeImmutable Immutable UTC creation timestamp.
     */
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    /**
     * Sets the creation timestamp.
     *
     * @param  \DateTimeImmutable $createdAt Immutable UTC creation timestamp.
     * @return static                         Fluent self for chaining.
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    /**
     * Returns the last-modification timestamp, or null.
     *
     * @return \DateTimeImmutable|null Immutable UTC last-modification timestamp, or null.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    /**
     * Sets the last-modification timestamp.
     *
     * @param  \DateTimeImmutable|null $updatedAt Immutable UTC last-modification timestamp, or null.
     * @return static                              Fluent self for chaining.
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }
}
