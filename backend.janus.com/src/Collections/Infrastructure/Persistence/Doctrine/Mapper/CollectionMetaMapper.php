<?php

/**
 * @file CollectionMetaMapper.php
 *
 * Data mapper translating between the CollectionMeta domain entity and the
 * CollectionMetaEntity Doctrine persistence model.
 *
 * @package App\Collections\Infrastructure\Persistence\Doctrine\Mapper
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Persistence\Doctrine\Mapper;

use App\Collections\Domain\Entity\CollectionMeta;
use App\Collections\Infrastructure\Persistence\Doctrine\Entity\CollectionMetaEntity;
use Symfony\Component\Uid\Uuid;

/**
 * Translates between the pure CollectionMeta domain entity and the Doctrine
 * CollectionMetaEntity persistence model in both directions.
 */
final readonly class CollectionMetaMapper
{
    /**
     * Converts a Doctrine CollectionMetaEntity to a pure domain CollectionMeta.
     *
     * @param  CollectionMetaEntity $entity The hydrated Doctrine persistence model to convert.
     * @return CollectionMeta                A domain entity reconstituted from the persisted record.
     */
    public function toDomain(CollectionMetaEntity $entity): CollectionMeta
    {
        return CollectionMeta::reconstitute(
            id:        (string) $entity->getId(),
            name:      $entity->getName(),
            label:     $entity->getLabel(),
            icon:      $entity->getIcon(),
            note:      $entity->getNote(),
            hidden:    $entity->isHidden(),
            singleton: $entity->isSingleton(),
            sortField: $entity->getSortField(),
            createdAt: $entity->getCreatedAt(),
            updatedAt: $entity->getUpdatedAt(),
        );
    }

    /**
     * Converts a domain CollectionMeta to a Doctrine CollectionMetaEntity ready for persistence.
     *
     * @param  CollectionMeta       $domain The domain entity to convert.
     * @return CollectionMetaEntity          A Doctrine model populated from the domain entity.
     */
    public function toPersistence(CollectionMeta $domain): CollectionMetaEntity
    {
        return (new CollectionMetaEntity())
            ->setId(Uuid::fromString($domain->getId()))
            ->setName($domain->getName())
            ->setLabel($domain->getLabel())
            ->setIcon($domain->getIcon())
            ->setNote($domain->getNote())
            ->setHidden($domain->isHidden())
            ->setSingleton($domain->isSingleton())
            ->setSortField($domain->getSortField())
            ->setCreatedAt($domain->getCreatedAt())
            ->setUpdatedAt($domain->getUpdatedAt());
    }
}
