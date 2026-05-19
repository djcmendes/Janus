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
use JsonSerializable;

/**
 * Immutable DTO representing a single Activity audit-log record.
 *
 * Constructed exclusively via ActivityDto::fromEntity() to ensure all
 * fields are populated from a hydrated Activity domain object.
 */
final readonly class ActivityDto implements JsonSerializable
{
    /**
     * Constructor
     *
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
        public string  $id,
        public string  $action,
        public ?string $collection,
        public ?string $item,
        public ?string $userId,
        public ?string $ip,
        public ?string $userAgent,
        public string  $timestamp,
    ) {}

    /**
     * Constructs an ActivityDto from a hydrated Activity domain entity.
     *
     * @param Activity $activity The Activity entity to convert.
     * @return self A fully-populated, immutable DTO.
     */
    public static function fromEntity(Activity $activity): self
    {
        return new self(
            id:         (string) $activity->id,
            action:     $activity->action,
            collection: $activity->collection,
            item:       $activity->item,
            userId:     $activity->userId,
            ip:         $activity->ip,
            userAgent:  $activity->userAgent,
            timestamp:  $activity->timestamp->format(format: DateTimeInterface::ATOM),
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

    /**
     * This method is called automatically by json_encode().
     *
     * @return array<string, mixed> JSON Serialized array of the DTO
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
