<?php

declare(strict_types=1);

namespace App\Schema\Domain\Service;

/**
 * Compares two schema snapshots and returns a structured diff describing what
 * would need to change to transform `$current` into `$target`.
 *
 * Diff shape:
 * {
 *   "collections": {
 *     "create": [ { collection, meta, fields } ],
 *     "update": [ { collection, diff: { ...changed meta keys } } ],
 *     "delete": [ "collection_name" ]
 *   },
 *   "fields": {
 *     "create": [ { collection, field, type, meta } ],
 *     "update": [ { collection, field, diff: { ...changed meta keys } } ],
 *     "delete": [ { collection, field } ]
 *   },
 *   "relations": {
 *     "create": [ { many_collection, many_field, ... } ],
 *     "update": [ { many_collection, many_field, diff: { ...changed keys } } ],
 *     "delete": [ { many_collection, many_field } ]
 *   }
 * }
 */
final class SchemaDiffService
{
    public function diff(array $current, array $target): array
    {
        return [
            'collections' => $this->diffCollections($current['collections'] ?? [], $target['collections'] ?? []),
            'fields'      => $this->diffFields($current['collections'] ?? [], $target['collections'] ?? []),
            'relations'   => $this->diffRelations($current['relations'] ?? [], $target['relations'] ?? []),
        ];
    }

    // ------------------------------------------------------------------ private

    private function diffCollections(array $current, array $target): array
    {
        $currentMap = $this->indexBy($current, 'collection');
        $targetMap  = $this->indexBy($target, 'collection');

        $create = [];
        $update = [];
        $delete = [];

        foreach ($targetMap as $name => $targetEntry) {
            if (!isset($currentMap[$name])) {
                $create[] = $targetEntry;
                continue;
            }

            $metaDiff = $this->metaDiff($currentMap[$name]['meta'] ?? [], $targetEntry['meta'] ?? []);
            if (!empty($metaDiff)) {
                $update[] = ['collection' => $name, 'diff' => $metaDiff];
            }
        }

        foreach ($currentMap as $name => $_) {
            if (!isset($targetMap[$name])) {
                $delete[] = $name;
            }
        }

        return compact('create', 'update', 'delete');
    }

    private function diffFields(array $current, array $target): array
    {
        // Build flat maps keyed by "collection::field"
        $currentFields = $this->flattenFields($current);
        $targetFields  = $this->flattenFields($target);

        $create = [];
        $update = [];
        $delete = [];

        foreach ($targetFields as $key => $targetEntry) {
            if (!isset($currentFields[$key])) {
                $create[] = $targetEntry;
                continue;
            }

            $diff = [];
            if ($currentFields[$key]['type'] !== $targetEntry['type']) {
                $diff['type'] = $targetEntry['type'];
            }
            $metaDiff = $this->metaDiff($currentFields[$key]['meta'] ?? [], $targetEntry['meta'] ?? []);
            if (!empty($metaDiff)) {
                $diff['meta'] = $metaDiff;
            }

            if (!empty($diff)) {
                $update[] = [
                    'collection' => $targetEntry['collection'],
                    'field'      => $targetEntry['field'],
                    'diff'       => $diff,
                ];
            }
        }

        foreach ($currentFields as $key => $currentEntry) {
            if (!isset($targetFields[$key])) {
                $delete[] = ['collection' => $currentEntry['collection'], 'field' => $currentEntry['field']];
            }
        }

        return compact('create', 'update', 'delete');
    }

    private function diffRelations(array $current, array $target): array
    {
        $currentMap = $this->indexByRelationKey($current);
        $targetMap  = $this->indexByRelationKey($target);

        $create = [];
        $update = [];
        $delete = [];

        foreach ($targetMap as $key => $targetEntry) {
            if (!isset($currentMap[$key])) {
                $create[] = $targetEntry;
                continue;
            }

            $diff = $this->metaDiff(
                $this->relationComparableFields($currentMap[$key]),
                $this->relationComparableFields($targetEntry),
            );

            if (!empty($diff)) {
                $update[] = [
                    'many_collection' => $targetEntry['many_collection'],
                    'many_field'      => $targetEntry['many_field'],
                    'diff'            => $diff,
                ];
            }
        }

        foreach ($currentMap as $key => $currentEntry) {
            if (!isset($targetMap[$key])) {
                $delete[] = [
                    'many_collection' => $currentEntry['many_collection'],
                    'many_field'      => $currentEntry['many_field'],
                ];
            }
        }

        return compact('create', 'update', 'delete');
    }

    // ------------------------------------------------------------------ helpers

    private function indexBy(array $items, string $key): array
    {
        $result = [];
        foreach ($items as $item) {
            $result[$item[$key]] = $item;
        }
        return $result;
    }

    private function indexByRelationKey(array $relations): array
    {
        $result = [];
        foreach ($relations as $rel) {
            $key          = $rel['many_collection'] . '::' . $rel['many_field'];
            $result[$key] = $rel;
        }
        return $result;
    }

    private function flattenFields(array $collections): array
    {
        $result = [];
        foreach ($collections as $col) {
            foreach ($col['fields'] ?? [] as $field) {
                $key          = $col['collection'] . '::' . $field['field'];
                $result[$key] = array_merge($field, ['collection' => $col['collection']]);
            }
        }
        return $result;
    }

    private function metaDiff(array $current, array $target): array
    {
        $diff = [];
        foreach ($target as $key => $value) {
            if (!array_key_exists($key, $current) || $current[$key] !== $value) {
                $diff[$key] = $value;
            }
        }
        return $diff;
    }

    private function relationComparableFields(array $relation): array
    {
        return [
            'one_collection'      => $relation['one_collection'] ?? null,
            'one_field'           => $relation['one_field'] ?? null,
            'junction_collection' => $relation['junction_collection'] ?? null,
        ];
    }
}
