<?php

/**
 * @file FieldMetaMapper.php
 *
 * Bi-directional mapper between the FieldMeta domain entity and FieldMetaEntity (Doctrine ORM).
 *
 * @package App\Fields\Infrastructure\Persistence\Doctrine\Mapper
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Infrastructure\Persistence\Doctrine\Mapper;

use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Infrastructure\Persistence\Doctrine\Entity\FieldMetaEntity;

/**
 * Converts between the pure domain FieldMeta and its Doctrine persistence counterpart.
 *
 * toDomain() uses FieldMeta::reconstitute() to avoid side effects (no new UUID, no timestamp reset).
 * toPersistence() builds a fresh FieldMetaEntity hydrated from the domain object's current state.
 */
final class FieldMetaMapper
{
    /**
     * Converts a Doctrine FieldMetaEntity to a domain FieldMeta.
     *
     * @param  FieldMetaEntity $entity The Doctrine-managed persistence record.
     * @return FieldMeta                The equivalent pure domain entity.
     */
    public function toDomain(FieldMetaEntity $entity): FieldMeta
    {
        return FieldMeta::reconstitute(
            id:         $entity->getId(),
            collection: $entity->getCollection(),
            field:      $entity->getField(),
            type:       $entity->getType(),
            label:      $entity->getLabel(),
            note:       $entity->getNote(),
            required:   $entity->isRequired(),
            readonly:   $entity->isReadonly(),
            hidden:     $entity->isHidden(),
            sortOrder:  $entity->getSortOrder(),
            interface:  $entity->getInterface(),
            options:    $entity->getOptions(),
            createdAt:  $entity->getCreatedAt(),
            updatedAt:  $entity->getUpdatedAt(),
        );
    }

    /**
     * Converts a domain FieldMeta to a Doctrine FieldMetaEntity ready for persistence.
     *
     * @param  FieldMeta       $domain The domain entity to persist.
     * @return FieldMetaEntity          A hydrated Doctrine entity.
     */
    public function toPersistence(FieldMeta $domain): FieldMetaEntity
    {
        return (new FieldMetaEntity())
            ->setId($domain->getId())
            ->setCollection($domain->getCollection())
            ->setField($domain->getField())
            ->setType($domain->getType())
            ->setLabel($domain->getLabel())
            ->setNote($domain->getNote())
            ->setRequired($domain->isRequired())
            ->setReadonly($domain->isReadonly())
            ->setHidden($domain->isHidden())
            ->setSortOrder($domain->getSortOrder())
            ->setInterface($domain->getInterface())
            ->setOptions($domain->getOptions())
            ->setCreatedAt($domain->getCreatedAt())
            ->setUpdatedAt($domain->getUpdatedAt());
    }
}
