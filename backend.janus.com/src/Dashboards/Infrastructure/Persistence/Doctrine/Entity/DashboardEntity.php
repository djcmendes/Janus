<?php

/**
 * @file DashboardEntity.php
 *
 * Doctrine ORM persistence model for the `dashboards` table.
 * This class is the sole owner of all database-mapping concerns for dashboard records.
 * Domain logic lives exclusively in Dashboard (Domain\Entity).
 *
 * @package App\Dashboards\Infrastructure\Persistence\Doctrine\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine entity mapping dashboard records to the `dashboards` table.
 *
 * Non-final to allow Doctrine proxy subclass generation. All persistence
 * concerns are confined here; the domain Dashboard class has no framework ties.
 */
#[ORM\Entity]
#[ORM\Table(name: 'dashboards')]
class DashboardEntity
{
    /** @var string UUIDv7 string primary key. */
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    /** @var string Human-readable display name. */
    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    /** @var string|null Optional icon identifier. */
    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $icon;

    /** @var string|null Optional descriptive note. */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $note;

    /** @var string|null Owner user UUID; null indicates a shared/global dashboard. */
    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $userId;

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
     * Sets the UUIDv7 string primary key.
     *
     * @param  string $id UUID string.
     * @return static      Fluent self for chaining.
     */
    public function setId(string $id): static { $this->id = $id; return $this; }

    /**
     * Returns the human-readable display name.
     *
     * @return string Dashboard name.
     */
    public function getName(): string { return $this->name; }

    /**
     * Sets the human-readable display name.
     *
     * @param  string $name Dashboard name.
     * @return static        Fluent self for chaining.
     */
    public function setName(string $name): static { $this->name = $name; return $this; }

    /**
     * Returns the optional icon identifier.
     *
     * @return string|null Icon identifier, or null if not set.
     */
    public function getIcon(): ?string { return $this->icon; }

    /**
     * Sets the optional icon identifier.
     *
     * @param  string|null $icon Icon identifier, or null to clear.
     * @return static              Fluent self for chaining.
     */
    public function setIcon(?string $icon): static { $this->icon = $icon; return $this; }

    /**
     * Returns the optional descriptive note.
     *
     * @return string|null Descriptive note, or null if not set.
     */
    public function getNote(): ?string { return $this->note; }

    /**
     * Sets the optional descriptive note.
     *
     * @param  string|null $note Descriptive note, or null to clear.
     * @return static              Fluent self for chaining.
     */
    public function setNote(?string $note): static { $this->note = $note; return $this; }

    /**
     * Returns the owner user UUID, or null for a shared/global dashboard.
     *
     * @return string|null Owner UUID string, or null.
     */
    public function getUserId(): ?string { return $this->userId; }

    /**
     * Sets the owner user UUID.
     *
     * @param  string|null $userId Owner UUID string, or null.
     * @return static                Fluent self for chaining.
     */
    public function setUserId(?string $userId): static { $this->userId = $userId; return $this; }

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
     * Returns the last-modification timestamp.
     *
     * @return \DateTimeImmutable Immutable UTC last-modification timestamp.
     */
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    /**
     * Sets the last-modification timestamp.
     *
     * @param  \DateTimeImmutable $updatedAt Immutable UTC last-modification timestamp.
     * @return static                         Fluent self for chaining.
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }
}
