<?php

/**
 * @file Dashboard.php
 *
 * Pure domain entity representing a user dashboard. Zero framework dependencies.
 *
 * @package App\Dashboards\Domain\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Domain\Entity;

use Symfony\Component\Uid\Uuid;

/**
 * Aggregate root for a named dashboard owned by a user or shared globally.
 *
 * The constructor generates a new UUIDv7 identity; use reconstitute() to
 * reload an existing record from persistence without creating a new ID.
 */
final class Dashboard
{
    /** @var string UUIDv7 string primary key. */
    private string $id;

    /** @var string Human-readable display name. */
    private string $name;

    /** @var string|null Optional icon identifier. */
    private ?string $icon;

    /** @var string|null Optional descriptive note. */
    private ?string $note;

    /** @var string|null Owner user UUID; null indicates a shared/global dashboard. */
    private ?string $userId;

    /** @var \DateTimeImmutable Creation timestamp (UTC). */
    private \DateTimeImmutable $createdAt;

    /** @var \DateTimeImmutable Last-modification timestamp (UTC). */
    private \DateTimeImmutable $updatedAt;

    /**
     * Creates a new Dashboard with a generated UUIDv7 identity.
     *
     * @param string      $name   Human-readable display name.
     * @param string|null $icon   Optional icon identifier.
     * @param string|null $note   Optional descriptive note.
     * @param string|null $userId Owner user UUID; null = shared/global.
     */
    public function __construct(
        string  $name,
        ?string $icon   = null,
        ?string $note   = null,
        ?string $userId = null,
    ) {
        $this->id        = (string) Uuid::v7();
        $this->name      = $name;
        $this->icon      = $icon;
        $this->note      = $note;
        $this->userId    = $userId;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Reconstitutes an existing Dashboard from persisted data without generating a new ID.
     *
     * @param string             $id        Existing UUIDv7 string.
     * @param string             $name      Human-readable display name.
     * @param string|null        $icon      Optional icon identifier.
     * @param string|null        $note      Optional descriptive note.
     * @param string|null        $userId    Owner user UUID; null = shared/global.
     * @param \DateTimeImmutable $createdAt Original creation timestamp.
     * @param \DateTimeImmutable $updatedAt Last-modification timestamp.
     *
     * @return self A Dashboard instance reflecting the persisted state.
     */
    public static function reconstitute(
        string             $id,
        string             $name,
        ?string            $icon,
        ?string            $note,
        ?string            $userId,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        $self            = new self($name, $icon, $note, $userId);
        $self->id        = $id;
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
     * Returns the human-readable display name.
     *
     * @return string Dashboard name.
     */
    public function getName(): string { return $this->name; }

    /**
     * Returns the optional icon identifier.
     *
     * @return string|null Icon identifier, or null if not set.
     */
    public function getIcon(): ?string { return $this->icon; }

    /**
     * Returns the optional descriptive note.
     *
     * @return string|null Descriptive note, or null if not set.
     */
    public function getNote(): ?string { return $this->note; }

    /**
     * Returns the owner user UUID, or null when the dashboard is shared/global.
     *
     * @return string|null Owner UUID string, or null.
     */
    public function getUserId(): ?string { return $this->userId; }

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
     * Updates the display name and refreshes the updatedAt timestamp.
     *
     * @param  string $name New display name.
     * @return static        Fluent self for chaining.
     */
    public function setName(string $name): static
    {
        $this->name      = $name;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * Updates the icon identifier and refreshes the updatedAt timestamp.
     *
     * @param  string|null $icon New icon identifier, or null to clear.
     * @return static              Fluent self for chaining.
     */
    public function setIcon(?string $icon): static
    {
        $this->icon      = $icon;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * Updates the descriptive note and refreshes the updatedAt timestamp.
     *
     * @param  string|null $note New note text, or null to clear.
     * @return static              Fluent self for chaining.
     */
    public function setNote(?string $note): static
    {
        $this->note      = $note;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * Returns true if this dashboard is owned by the given user UUID.
     *
     * @param  string $userId UUID of the user to check.
     * @return bool           True when the dashboard belongs to that user.
     */
    public function isOwnedBy(string $userId): bool
    {
        return $this->userId === $userId;
    }
}
