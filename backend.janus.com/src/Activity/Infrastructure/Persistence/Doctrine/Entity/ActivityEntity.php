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

use DateTimeImmutable;
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
    /**
     * Doctrine-managed UUID value object representing primary key of this record.
     * @var Uuid
     */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private(set) Uuid $id
    {
        get => $this->id;
        set => $this->id = $value;
    }

    /**
     * Action type to record (e.g. 'create', 'update', 'delete').
     * @var string
     */
    #[ORM\Column(length: 50)]
    public string $action
    {
        get => $this->action;
        set => $this->action = $value;
    }

    /**
     * Collection name the action targeted, or null when not collection-scoped.
     * @var string|null
     */
    #[ORM\Column(length: 200, nullable: true)]
    public ?string $collection = null
    {
        get => $this->collection;
        set => $this->collection = $value;
    }

    /**
     * Primary key of the item the action targeted, or null when not item-scoped.
     * @var string|null
     */
    #[ORM\Column(nullable: true)]
    public ?string $item = null
    {
        get => $this->item;
        set => $this->item = $value;
    }

    /**
     * UUID of the user who performed the action, or null for anonymous/system actions.
     * @var string|null
     */
    #[ORM\Column(nullable: true)]
    public ?string $userId = null
    {
        get => $this->userId;
        set => $this->userId = $value;
    }

    /**
     * IP address of the originating request, or null when not recorded.
     * @var string|null
     */
    #[ORM\Column(nullable: true)]
    public ?string $ip = null
    {
        get => $this->ip;
        set => $this->ip = $value;
    }

    /**
     * User-Agent string of the originating request, or null when not recorded.
     * @var string|null
     */
    #[ORM\Column(nullable: true)]
    public ?string $userAgent = null
    {
        get => $this->userAgent;
        set => $this->userAgent = $value;
    }

    /**
     * Immutable UTC timestamp of when the activity was recorded
     * @var DateTimeImmutable
     */
    #[ORM\Column]
    public DateTimeImmutable $timestamp
    {
        get => $this->timestamp;
        set => $this->timestamp = $value;
    }

    /**
     * Sets the unique identifier for the activity.
     *
     * @param Uuid $id The UUID to assign as the primary key.
     * @return static Return self for chaining
     */
    public function setId(Uuid $id): static
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set activity action to store
     *
     * @param string $action Action type to store.
     * @return static Return self for chaining
     */
    public function setAction(string $action): static {
        $this->action = $action;
        return $this;
    }

    /**
     * Sets the collection name the action targeted.
     *
     * @param string|null $collection Collection name, or null to clear.
     * @return static Return self for chaining
     */
    public function setCollection(?string $collection): static {
        $this->collection = $collection;
        return $this;
    }

    /**
     * Sets the primary key of the item the action targeted.
     *
     * @param string|null $item Item identifier, or null to clear.
     * @return static Return self for chaining
     */
    public function setItem(?string $item): static
    {
        $this->item = $item;
        return $this;
    }

    /**
     * Sets the UUID of the user who performed the action.
     *
     * @param string|null $userId User UUID, or null to clear.
     * @return static Return self for chaining
     */
    public function setUserId(?string $userId): static
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Sets the IP address of the originating request.
     *
     * @param string|null $ip IP address string, or null to clear.
     * @return static Return self for chaining
     */
    public function setIp(?string $ip): static
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * Sets the User-Agent string of the originating request.
     *
     * @param string|null $userAgent User-Agent string, or null to clear.
     * @return static Return self for chaining
     */
    public function setUserAgent(?string $userAgent): static
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * Sets the UTC timestamp of when the activity was recorded.
     *
     * @param DateTimeImmutable $timestamp Immutable datetime in UTC.
     * @return static Return self for chaining
     */
    public function setTimestamp(DateTimeImmutable $timestamp): static
    {
        $this->timestamp = $timestamp;
        return $this;
    }
}
