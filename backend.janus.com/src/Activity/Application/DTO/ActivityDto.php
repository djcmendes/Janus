<?php

/**
 * @file ActivityDto.php
 *
 * Read-only data transfer object carrying a serialised Activity audit-log entry
 * from the application layer to the presentation layer.
 *
 * @package App\Activity\Application\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\DTO;

use App\Activity\Domain\Entity\Activity;
use DateTimeInterface;

/**
 * Immutable DTO representing a single Activity audit-log record.
 *
 * Constructed exclusively via ActivityDto::fromEntity() to ensure all
 * fields are populated from a hydrated Activity domain object.
 */
final class ActivityDto
{
    /**
     * @param string      $id         UUID of the activity record.
     * @param string      $action     Action type (e.g. 'create', 'update', 'delete').
     * @param string|null $collection Collection the action was performed on, or null.
     * @param string|null $item       Identifier of the affected item, or null.
     * @param string|null $userId     UUID of the user who performed the action, or null.
     * @param string|null $ip         IP address of the originating request, or null if unavailable.
     * @param string|null $userAgent  User-Agent string of the originating request, or null if unavailable.
     * @param string      $timestamp  ISO 8601 timestamp of when the activity occurred.
     */
    public function __construct(
        public readonly string  $id,
        public readonly string  $action,
        public readonly ?string $collection,
        public readonly ?string $item,
        public readonly ?string $userId,
        public readonly ?string $ip,
        public readonly ?string $userAgent,
        public readonly string  $timestamp,
    ) {}

    /**
     * Constructs an ActivityDto from a hydrated Activity domain entity.
     *
     * @param  Activity $a The Activity entity to convert.
     * @return self        A fully-populated, immutable DTO.
     */
    public static function fromEntity(Activity $a): self
    {
        return new self(
            id:         (string) $a->getId(),
            action:     $a->getAction(),
            collection: $a->getCollection(),
            item:       $a->getItem(),
            userId:     $a->getUserId(),
            ip:         $a->getIp(),
            userAgent:  $a->getUserAgent(),
            timestamp:  $a->getTimestamp()
                          ->format(DateTimeInterface::ATOM),
        );
    }

    /**
     * Serialises the DTO to an associative array for JSON encoding.
     *
     * @return array<string, string|null> Key-value map of all DTO fields.
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
            'timestamp'  => $this->timestamp,
        ];
    }
}
