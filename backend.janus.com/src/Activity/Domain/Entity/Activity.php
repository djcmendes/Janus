<?php

/**
 * @file Activity.php
 *
 * Pure domain entity representing a single audit-log entry.
 * Contains no framework or persistence dependencies.
 *
 * @package App\Activity\Domain\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Domain\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Audit-log record capturing who did what, on which resource, and from where.
 *
 * A UUIDv7 string identifier is generated on construction, providing
 * chronological ordering without a separate timestamp index on the id column.
 * All Doctrine mapping concerns live exclusively in ActivityEntity (Infrastructure layer).
 */
final class Activity
{
    /** UUID string of the persisted record.
     * @var string
     */
    private string $id;

    /** Action type.
     * @var string
     */
    private string $action;

    /**
     * Collection the action was performed on.
     * @var string|null
     */
    private ?string $collection;

    /**
     * Identifier of the affected item.
     * @var string|null
     */
    private ?string $item;

    /**
     * UUID of the user who performed the action.
     * @var string|null
     */
    private ?string $userId = null;

    /**
     * IP address of the originating request.
     * @var string|null
     */
    private ?string $ip = null;

    /**
     * User-Agent string of the originating request.
     * @var string|null
     */
    private ?string $userAgent = null;

    /**
     * Timestamp of when the activity occurred.
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $timestamp;

    /**
     * Constructor
     *
     * @param string      $action     Action type (e.g. 'create', 'update', 'delete', 'login').
     * @param string|null $collection Collection the action was performed on, or null.
     * @param string|null $item       Identifier of the affected item, or null.
     */
    public function __construct(string $action, ?string $collection = null, ?string $item = null)
    {
        $this->id         = (string) Uuid::v7();
        $this->action     = $action;
        $this->collection = $collection;
        $this->item       = $item;
        $this->timestamp  = new \DateTimeImmutable();
    }

    /**
     * Reconstructs an Activity from persisted data, bypassing the auto-generated
     * id and timestamp set by the constructor.
     *
     * Used exclusively by ActivityMapper when converting from ActivityEntity.
     *
     * @param string             $id         UUID string of the persisted record.
     * @param string             $action     Action type.
     * @param string|null        $collection Collection name, or null.
     * @param string|null        $item       Item identifier, or null.
     * @param string|null        $userId     User UUID, or null.
     * @param string|null        $ip         IP address, or null.
     * @param string|null        $userAgent  User-Agent string, or null.
     * @param DateTimeImmutable $timestamp  Original timestamp from persistence.
     *
     * @return self A fully-populated Activity with the given id and timestamp.
     */
    public static function reconstitute(
        string            $id,
        string            $action,
        ?string           $collection,
        ?string           $item,
        ?string           $userId,
        ?string           $ip,
        ?string           $userAgent,
        DateTimeImmutable $timestamp,
    ): self {
        $instance            = new self($action, $collection, $item);
        $instance->id        = $id;
        $instance->userId    = $userId;
        $instance->ip        = $ip;
        $instance->userAgent = $userAgent;
        $instance->timestamp = $timestamp;

        return $instance;
    }

    /**
     * Returns the unique UUID of this activity record.
     *
     * @return string UUID v4.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Returns the action that was performed (e.g. "create", "update", "delete").
     *
     * @return string Action identifier.
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Returns the name of the collection this action targeted, if applicable.
     *
     * @return string|null Collection name, or null when not collection-scoped.
     */
    public function getCollection(): ?string
    {
        return $this->collection;
    }

    /**
     * Returns the primary key of the item this action targeted, if applicable.
     *
     * @return string|null Item identifier, or null when not item-scoped.
     */
    public function getItem(): ?string
    {
        return $this->item;
    }

    /**
     * Returns the UUID of the user who performed the action, if authenticated.
     *
     * @return string|null User UUID, or null for anonymous/system actions.
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * Sets the user identifier.
     *
     * @param  string|null $v UUID of the user, or null to clear.
     * @return static
     */
    public function setUserId(?string $v): static
    {
        $this->userId = $v;
        return $this;
    }

    /**
     * Returns the IP address of the request that triggered the activity, if captured.
     *
     * @return string|null IP address string, or null when not recorded.
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * Sets the originating IP address.
     *
     * @param  string|null $v IP address string, or null to clear.
     * @return static
     */
    public function setIp(?string $v): static
    {
        $this->ip = $v;
        return $this;
    }

    /**
     * Returns the User-Agent string of the request that triggered the activity, if captured.
     *
     * @return string|null User-Agent string, or null when not recorded.
     */
    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    /**
     * Sets the originating User-Agent string.
     *
     * @param  string|null $v User-Agent string, or null to clear.
     * @return static
     */
    public function setUserAgent(?string $v): static
    {
        $this->userAgent = $v;
        return $this;
    }

    /**
     * Returns the UTC timestamp of when the activity was recorded.
     *
     * @return DateTimeImmutable Immutable datetime in UTC.
     */
    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    /**
     * Serialises the entity to an associative array for JSON encoding.
     *
     * @return array<string, string|null> Key-value map of all entity fields.
     */
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'action'     => $this->action,
            'collection' => $this->collection,
            'item'       => $this->item,
            'user'       => $this->userId,
            'ip'         => $this->ip,
            'user_agent' => $this->userAgent,
            'timestamp'  => $this->timestamp->format(DateTimeInterface::ATOM),
        ];
    }
}
