<?php

/**
 * @file ExtensionMapper.php
 *
 * Data mapper translating between the Extension domain entity and the
 * ExtensionEntity Doctrine persistence model.
 *
 * @package App\Extensions\Infrastructure\Persistence\Doctrine\Mapper
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Infrastructure\Persistence\Doctrine\Mapper;

use App\Extensions\Domain\Entity\Extension;
use App\Extensions\Infrastructure\Persistence\Doctrine\Entity\ExtensionEntity;

/**
 * Translates between the pure Extension domain entity and the Doctrine
 * ExtensionEntity persistence model in both directions.
 */
final readonly class ExtensionMapper
{
    /**
     * Converts a Doctrine ExtensionEntity to a pure domain Extension.
     *
     * @param  ExtensionEntity $entity The hydrated Doctrine persistence model to convert.
     * @return Extension                A domain entity reconstituted from the persisted record.
     */
    public function toDomain(ExtensionEntity $entity): Extension
    {
        return Extension::reconstitute(
            id:          $entity->getId(),
            name:        $entity->getName(),
            type:        $entity->getType(),
            version:     $entity->getVersion(),
            enabled:     $entity->isEnabled(),
            description: $entity->getDescription(),
            meta:        $entity->getMeta(),
            createdAt:   $entity->getCreatedAt(),
            updatedAt:   $entity->getUpdatedAt(),
        );
    }

    /**
     * Converts a domain Extension to a Doctrine ExtensionEntity ready for persistence.
     *
     * @param  Extension       $domain The domain entity to convert.
     * @return ExtensionEntity          A Doctrine model populated from the domain entity.
     */
    public function toPersistence(Extension $domain): ExtensionEntity
    {
        return (new ExtensionEntity())
            ->setId($domain->getId())
            ->setName($domain->getName())
            ->setType($domain->getType())
            ->setVersion($domain->getVersion())
            ->setEnabled($domain->isEnabled())
            ->setDescription($domain->getDescription())
            ->setMeta($domain->getMeta())
            ->setCreatedAt($domain->getCreatedAt())
            ->setUpdatedAt($domain->getUpdatedAt());
    }
}
