<?php

/**
 * @file SchemaDiffServiceTest.php
 *
 * Abstract base for SchemaDiffService test suites.
 *
 * @package App\Schema\Domain\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Domain\Service\Tests;

use App\Schema\Domain\Service\SchemaDiffService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Shared setup and snapshot factory helpers for all SchemaDiffService test suites.
 */
#[CoversClass(className: SchemaDiffService::class)]
abstract class SchemaDiffServiceTest extends TestCase
{
    protected SchemaDiffService $class;

    public function setUp(): void
    {
        $this->class = new SchemaDiffService();
    }

    public function tearDown(): void
    {
        unset($this->class);
    }

    /** Returns an empty snapshot (no collections, no relations). */
    protected function emptySnapshot(): array
    {
        return ['version' => 1, 'collections' => [], 'relations' => []];
    }

    /** Builds a minimal snapshot with one collection and no fields or relations. */
    protected function snapshotWithCollection(string $name, array $meta = []): array
    {
        return [
            'version'     => 1,
            'collections' => [[
                'collection' => $name,
                'meta'       => array_merge(['label' => null, 'icon' => null, 'note' => null, 'hidden' => false, 'singleton' => false, 'sort_field' => null], $meta),
                'fields'     => [],
            ]],
            'relations' => [],
        ];
    }

    /** Builds a minimal snapshot with one collection containing one field. */
    protected function snapshotWithField(string $collection, string $field, string $type, array $meta = []): array
    {
        $snap = $this->snapshotWithCollection($collection);
        $snap['collections'][0]['fields'][] = [
            'field' => $field,
            'type'  => $type,
            'meta'  => array_merge(['label' => null, 'note' => null, 'required' => false, 'readonly' => false, 'hidden' => false, 'sort_order' => 0], $meta),
        ];

        return $snap;
    }

    /** Builds a minimal snapshot with one relation. */
    protected function snapshotWithRelation(string $manyCollection, string $manyField, ?string $oneCollection = null): array
    {
        return [
            'version'     => 1,
            'collections' => [],
            'relations'   => [[
                'many_collection'     => $manyCollection,
                'many_field'          => $manyField,
                'one_collection'      => $oneCollection,
                'one_field'           => null,
                'junction_collection' => null,
            ]],
        ];
    }
}
