<?php

/**
 * @file ActivityMapper.php
 *
 * Data mapper translating between the Activity domain entity and the
 * ActivityEntity Doctrine persistence model.
 *
 * @package App\Activity\Infrastructure\Persistence\Doctrine\Mapper
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Persistence\Doctrine\Mapper;

use App\Activity\Domain\Entity\Activity;
use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use Symfony\Component\Uid\Uuid;

/**
 * Translates between the pure Activity domain entity and the Doctrine
 * ActivityEntity persistence model in both directions.
 */
final readonly class ActivityMapper
{
    /**
     * Converts a Doctrine ActivityEntity to a pure domain Activity.
     *
     * @param ActivityEntity $entity The hydrated Doctrine persistence model to convert.
     * @return Activity A domain entity reconstituted from the persisted record.
     */
    public function toDomain(ActivityEntity $entity): Activity
    {
        return Activity::reconstitute(
            id:         (string) $entity->id,
            action:     $entity->action,
            collection: $entity->collection,
            item:       $entity->item,
            userId:     $entity->userId,
            ip:         $entity->ip,
            userAgent:  $entity->userAgent,
            timestamp:  $entity->timestamp,
        );
    }

    /**
     * Converts a domain Activity to a Doctrine ActivityEntity ready for persistence.
     *
     * @param Activity $domain The domain entity to convert.
     * @return ActivityEntity A Doctrine model populated from the domain entity.
     */
    public function toPersistence(Activity $domain): ActivityEntity
    {
        return (new ActivityEntity())->setId(id: Uuid::fromString(uuid: $domain->id))
                                     ->setAction(action: $domain->action)
                                     ->setCollection(collection: $domain->collection)
                                     ->setItem(item: $domain->item)
                                     ->setUserId(userId: $domain->userId)
                                     ->setIp(ip: $domain->ip)
                                     ->setUserAgent(userAgent: $domain->userAgent)
                                     ->setTimestamp(timestamp: $domain->timestamp);
    }
}
