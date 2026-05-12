<?php

/**
 * @file VersionMapper.php
 *
 * Data mapper translating between the Version domain entity and the
 * VersionEntity Doctrine persistence model.
 *
 * @package App\Versions\Infrastructure\Persistence\Doctrine\Mapper
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Persistence\Doctrine\Mapper;

use App\Versions\Domain\Entity\Version;
use App\Versions\Infrastructure\Persistence\Doctrine\Entity\VersionEntity;
use Symfony\Component\Uid\Uuid;

/**
 * Translates between the pure Version domain entity and the Doctrine
 * VersionEntity persistence model in both directions.
 */
final class VersionMapper
{
    /**
     * Converts a Doctrine VersionEntity to a pure domain Version.
     *
     * @param  VersionEntity $entity The hydrated Doctrine persistence model to convert.
     * @return Version                A domain entity reconstituted from the persisted record.
     */
    public function toDomain(VersionEntity $entity): Version
    {
        return Version::reconstitute(
            id:         (string) $entity->getId(),
            collection: $entity->getCollection(),
            item:       $entity->getItem(),
            key:        $entity->getKey(),
            data:       $entity->getData(),
            delta:      $entity->getDelta(),
            userId:     $entity->getUserId(),
            createdAt:  $entity->getCreatedAt(),
            updatedAt:  $entity->getUpdatedAt(),
        );
    }

    /**
     * Converts a domain Version to a Doctrine VersionEntity ready for persistence.
     *
     * @param  Version       $domain The domain entity to convert.
     * @return VersionEntity          A Doctrine model populated from the domain entity.
     */
    public function toPersistence(Version $domain): VersionEntity
    {
        return (new VersionEntity())
            ->setId(Uuid::fromString($domain->getId()))
            ->setCollection($domain->getCollection())
            ->setItem($domain->getItem())
            ->setKey($domain->getKey())
            ->setData($domain->getData())
            ->setDelta($domain->getDelta())
            ->setUserId($domain->getUserId())
            ->setCreatedAt($domain->getCreatedAt())
            ->setUpdatedAt($domain->getUpdatedAt());
    }
}
