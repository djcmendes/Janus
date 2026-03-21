<?php

declare(strict_types=1);

namespace App\Schema\Domain\Service;

use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;
use App\Relations\Domain\Repository\RelationRepositoryInterface;

/**
 * Assembles a complete schema snapshot from the metadata repositories.
 *
 * Snapshot shape:
 * {
 *   "version": 1,
 *   "collections": [
 *     {
 *       "collection": "articles",
 *       "meta": { "label": ..., "icon": ..., "note": ..., "hidden": ..., "singleton": ..., "sort_field": ... },
 *       "fields": [
 *         { "field": "id", "type": "uuid", "meta": { "label": ..., ... } }
 *       ]
 *     }
 *   ],
 *   "relations": [
 *     { "many_collection": ..., "many_field": ..., "one_collection": ..., "one_field": ..., "junction_collection": ... }
 *   ]
 * }
 */
final class SchemaSnapshotService
{
    private const SNAPSHOT_VERSION = 1;
    private const MAX_ROWS         = 10_000;

    public function __construct(
        private readonly CollectionMetaRepositoryInterface $collectionRepository,
        private readonly FieldMetaRepositoryInterface      $fieldRepository,
        private readonly RelationRepositoryInterface       $relationRepository,
    ) {}

    public function snapshot(): array
    {
        $collections = $this->collectionRepository->findPaginated(self::MAX_ROWS, 0);
        $allFields   = $this->fieldRepository->findPaginated(self::MAX_ROWS, 0);
        $relations   = $this->relationRepository->findPaginated(self::MAX_ROWS, 0);

        // Index fields by collection for fast lookup
        $fieldsByCollection = [];
        foreach ($allFields as $field) {
            $fieldsByCollection[$field->getCollection()][] = [
                'field' => $field->getField(),
                'type'  => $field->getType()->value,
                'meta'  => [
                    'label'      => $field->getLabel(),
                    'note'       => $field->getNote(),
                    'required'   => $field->isRequired(),
                    'readonly'   => $field->isReadonly(),
                    'hidden'     => $field->isHidden(),
                    'sort_order' => $field->getSortOrder(),
                ],
            ];
        }

        $collectionsData = [];
        foreach ($collections as $collection) {
            $collectionsData[] = [
                'collection' => $collection->getName(),
                'meta'       => [
                    'label'      => $collection->getLabel(),
                    'icon'       => $collection->getIcon(),
                    'note'       => $collection->getNote(),
                    'hidden'     => $collection->isHidden(),
                    'singleton'  => $collection->isSingleton(),
                    'sort_field' => $collection->getSortField(),
                ],
                'fields' => $fieldsByCollection[$collection->getName()] ?? [],
            ];
        }

        $relationsData = [];
        foreach ($relations as $relation) {
            $relationsData[] = [
                'many_collection'    => $relation->getManyCollection(),
                'many_field'         => $relation->getManyField(),
                'one_collection'     => $relation->getOneCollection(),
                'one_field'          => $relation->getOneField(),
                'junction_collection'=> $relation->getJunctionCollection(),
            ];
        }

        return [
            'version'     => self::SNAPSHOT_VERSION,
            'collections' => $collectionsData,
            'relations'   => $relationsData,
        ];
    }
}
