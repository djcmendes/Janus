<?php

/**
 * @file SchemaSnapshotServiceSnapshotTest.php
 *
 * Tests for SchemaSnapshotService::snapshot().
 *
 * @package App\Schema\Domain\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Domain\Service\Tests;

use App\Collections\Domain\Entity\CollectionMeta;
use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Enum\FieldType;
use App\Relations\Domain\Entity\Relation;
use App\Schema\Domain\Service\SchemaSnapshotService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies snapshot() assembles the full schema snapshot from its repositories.
 *
 * Domain entities (CollectionMeta, FieldMeta, Relation) are all final; they are
 * constructed as real objects rather than mocks.
 */
#[CoversClass(className: SchemaSnapshotService::class)]
#[CoversMethod(SchemaSnapshotService::class, 'snapshot')]
final class SchemaSnapshotServiceSnapshotTest extends SchemaSnapshotServiceTest
{
    public function testSnapshotReturnsArray(): void
    {
        $this->assertIsArray($this->class->snapshot());
    }

    public function testSnapshotHasVersionKey(): void
    {
        $this->assertArrayHasKey('version', $this->class->snapshot());
    }

    public function testSnapshotHasCollectionsKey(): void
    {
        $this->assertArrayHasKey('collections', $this->class->snapshot());
    }

    public function testSnapshotHasRelationsKey(): void
    {
        $this->assertArrayHasKey('relations', $this->class->snapshot());
    }

    public function testSnapshotVersionIsOne(): void
    {
        $this->assertSame(1, $this->class->snapshot()['version']);
    }

    public function testSnapshotCollectionsIsEmptyWhenNoCollections(): void
    {
        $this->assertSame([], $this->class->snapshot()['collections']);
    }

    public function testSnapshotIncludesCollectionName(): void
    {
        $this->collectionReturn = [new CollectionMeta('articles')];

        $snapshot = $this->class->snapshot();

        $this->assertCount(1, $snapshot['collections']);
        $this->assertSame('articles', $snapshot['collections'][0]['collection']);
    }

    public function testSnapshotCollectionHasMetaKey(): void
    {
        $this->collectionReturn = [new CollectionMeta('articles')];

        $snapshot = $this->class->snapshot();

        $this->assertArrayHasKey('meta', $snapshot['collections'][0]);
    }

    public function testSnapshotCollectionMetaHasLabelKey(): void
    {
        $this->collectionReturn = [new CollectionMeta('articles')];

        $snapshot = $this->class->snapshot();

        $this->assertArrayHasKey('label', $snapshot['collections'][0]['meta']);
    }

    public function testSnapshotCollectionIncludesItsFields(): void
    {
        $this->collectionReturn = [new CollectionMeta('articles')];
        $this->fieldReturn      = [new FieldMeta('articles', 'title', FieldType::STRING)];

        $snapshot = $this->class->snapshot();

        $this->assertCount(1, $snapshot['collections'][0]['fields']);
        $this->assertSame('title', $snapshot['collections'][0]['fields'][0]['field']);
        $this->assertSame('string', $snapshot['collections'][0]['fields'][0]['type']);
    }

    public function testSnapshotCollectionFieldsIsEmptyWhenNoFields(): void
    {
        $this->collectionReturn = [new CollectionMeta('articles')];

        $snapshot = $this->class->snapshot();

        $this->assertSame([], $snapshot['collections'][0]['fields']);
    }

    public function testSnapshotIncludesRelation(): void
    {
        $relation = new Relation('articles', 'author_id');
        $relation->setOneCollection('users');
        $this->relationReturn = [$relation];

        $snapshot = $this->class->snapshot();

        $this->assertCount(1, $snapshot['relations']);
        $this->assertSame('articles', $snapshot['relations'][0]['many_collection']);
        $this->assertSame('users', $snapshot['relations'][0]['one_collection']);
    }

    public function testSnapshotRelationsIsEmptyWhenNoRelations(): void
    {
        $this->assertSame([], $this->class->snapshot()['relations']);
    }
}
