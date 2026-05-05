<?php

/**
 * @file ActivityEntity.php
 *
 * Doctrine ORM persistence model for the `activity` table.
 * This class is the sole owner of all database-mapping concerns for activity records.
 * Domain logic lives exclusively in Activity (Domain\Entity).
 *
 * @package App\Activity\Infrastructure\Persistence\Doctrine\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Doctrine entity mapping activity audit-log records to the `activity` table.
 *
 * Non-final to allow Doctrine proxy subclass generation. All persistence
 * concerns are confined here; the domain Activity class has no framework ties.
 */
#[ORM\Entity]
#[ORM\Table(name: 'activity')]
class ActivityEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(length: 50)]
    private string $action;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $collection = null;

    #[ORM\Column(nullable: true)]
    private ?string $item = null;

    #[ORM\Column(nullable: true)]
    private ?string $userId = null;

    #[ORM\Column(nullable: true)]
    private ?string $ip = null;

    #[ORM\Column(nullable: true)]
    private ?string $userAgent = null;

    #[ORM\Column]
    private \DateTimeImmutable $timestamp;

    /**
     * Returns the UUID primary key of this record.
     *
     * @return Uuid Doctrine-managed UUID value object.
     */
    public function getId(): Uuid { return $this->id; }

    /**
     * @param Uuid $id The UUID to assign as the primary key.
     *
     * @return static
     */
    public function setId(Uuid $id): static { $this->id = $id; return $this; }

    /**
     * @return string Action type (e.g. 'create', 'update', 'delete').
     */
    public function getAction(): string { return $this->action; }

    /**
     * @param string $action Action type to store.
     *
     * @return static
     */
    public function setAction(string $action): static { $this->action = $action; return $this; }

    /**
     * Returns the collection name the action targeted, or null when not collection-scoped.
     *
     * @return string|null Collection name.
     */
    public function getCollection(): ?string { return $this->collection; }

    /**
     * Sets the collection name the action targeted.
     *
     * @param  string|null $collection Collection name, or null to clear.
     * @return static
     */
    public function setCollection(?string $collection): static { $this->collection = $collection; return $this; }

    /**
     * Returns the primary key of the item the action targeted, or null when not item-scoped.
     *
     * @return string|null Item identifier.
     */
    public function getItem(): ?string { return $this->item; }

    /**
     * Sets the primary key of the item the action targeted.
     *
     * @param  string|null $item Item identifier, or null to clear.
     * @return static
     */
    public function setItem(?string $item): static { $this->item = $item; return $this; }

    /**
     * Returns the UUID of the user who performed the action, or null for anonymous/system actions.
     *
     * @return string|null User UUID.
     */
    public function getUserId(): ?string { return $this->userId; }

    /**
     * Sets the UUID of the user who performed the action.
     *
     * @param  string|null $userId User UUID, or null to clear.
     * @return static
     */
    public function setUserId(?string $userId): static { $this->userId = $userId; return $this; }

    /**
     * Returns the IP address of the originating request, or null when not recorded.
     *
     * @return string|null IP address string.
     */
    public function getIp(): ?string { return $this->ip; }

    /**
     * Sets the IP address of the originating request.
     *
     * @param  string|null $ip IP address string, or null to clear.
     * @return static
     */
    public function setIp(?string $ip): static { $this->ip = $ip; return $this; }

    /**
     * Returns the User-Agent string of the originating request, or null when not recorded.
     *
     * @return string|null User-Agent string.
     */
    public function getUserAgent(): ?string { return $this->userAgent; }

    /**
     * Sets the User-Agent string of the originating request.
     *
     * @param  string|null $userAgent User-Agent string, or null to clear.
     * @return static
     */
    public function setUserAgent(?string $userAgent): static { $this->userAgent = $userAgent; return $this; }

    /**
     * Returns the UTC timestamp of when the activity was recorded.
     *
     * @return \DateTimeImmutable Immutable datetime in UTC.
     */
    public function getTimestamp(): \DateTimeImmutable { return $this->timestamp; }

    /**
     * Sets the UTC timestamp of when the activity was recorded.
     *
     * @param  \DateTimeImmutable $timestamp Immutable datetime in UTC.
     * @return static
     */
    public function setTimestamp(\DateTimeImmutable $timestamp): static { $this->timestamp = $timestamp; return $this; }
}
