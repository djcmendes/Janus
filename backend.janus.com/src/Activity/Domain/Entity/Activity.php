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
    /**
     * UUID string of the persisted record.
     * @var string
     */
    private(set) string $id;

    /**
     * Action type (e.g. 'create', 'update', 'delete', 'login').
     * @var string
     */
    public string $action
    {
        get => $this->action;
    }

    /**
     * Collection the action was performed on, or null.
     * @var string|null
     */
    public ?string $collection = null
    {
        get => $this->collection;
    }

    /**
     * Identifier of the affected item, or null.
     * @var string|null
     */
    public ?string $item = null
    {
        get => $this->item;
    }

    /**
     * UUID of the user who performed the action, if authenticated.
     * @var string|null
     */
    public ?string $userId = null
    {
        get => $this->userId;
        set => $this->userId = $value;
    }

    /**
     * IP address of the originating request that triggered the activity, if captured.
     * @var string|null
     */
    public ?string $ip = null
    {
        get => $this->ip;
        set => $this->ip = $value;
    }

    /**
     * User-Agent string of the originating request that triggered the activity, if captured.
     * @var string|null
     */
    public ?string $userAgent = null
    {
        get => $this->userAgent;
        set => $this->userAgent = $value;
    }

    /**
     * Timestamp of when the activity occurred.
     * @var DateTimeImmutable
     */
    public DateTimeImmutable $timestamp
    {
        get => $this->timestamp;
    }

    /**
     * Constructor
     *
     * @param string      $action     Action type (e.g. 'create', 'update', 'delete', 'login').
     * @param string|null $collection Collection the action was performed on, or null.
     * @param string|null $item       Identifier of the affected item, or null.
     */
    public function __construct(
        string  $action,
        ?string $collection = null,
        ?string $item       = null,
    )
    {
        $this->id         = (string) Uuid::v7();
        $this->action     = $action;
        $this->collection = $collection;
        $this->item       = $item;
        $this->timestamp  = new DateTimeImmutable();
    }

    /**
     * Reconstructs an Activity from persisted data, bypassing the auto-generated
     * id and timestamp set by the constructor.
     *
     * Used exclusively by ActivityMapper when converting from ActivityEntity.
     *
     * @param string            $id         UUID string of the persisted record.
     * @param string            $action     Action type.
     * @param string|null       $collection Collection name, or null.
     * @param string|null       $item       Item identifier, or null.
     * @param string|null       $userId     User UUID, or null.
     * @param string|null       $ip         IP address, or null.
     * @param string|null       $userAgent  User-Agent string, or null.
     * @param DateTimeImmutable $timestamp  Original timestamp from persistence.
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
     * Sets the user identifier.
     *
     * @param string|null $userId UUID of the user, or null to clear.
     * @return static Return self for chaining
     */
    public function setUserId(?string $userId): static
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Sets the originating IP address.
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
     * Sets the originating User-Agent string.
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
            'timestamp'  => $this->timestamp->format(format: DateTimeInterface::ATOM),
        ];
    }
}
