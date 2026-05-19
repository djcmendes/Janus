<?php

/**
 * @file ApplySchemaHandlerHandleTest.php
 *
 * Tests for ApplySchemaHandler::handle().
 *
 * @package App\Schema\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Application\Command\Handler\Tests;

use App\Collections\Domain\Entity\CollectionMeta;
use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Enum\FieldType;
use App\Relations\Domain\Entity\Relation;
use App\Schema\Application\Command\ApplySchemaCommand;
use App\Schema\Application\Command\Handler\ApplySchemaHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies handle() applies diff operations and returns the expected applied/skipped log.
 *
 * Each test configures repository mocks to produce a specific diff scenario:
 * - Current snapshot is assembled from findPaginated() return values.
 * - Target snapshot is embedded in the ApplySchemaCommand.
 * - The real SchemaDiffService computes the diff; the real sub-handlers execute it.
 */
#[CoversClass(className: ApplySchemaHandler::class)]
#[CoversMethod(ApplySchemaHandler::class, 'handle')]
final class ApplySchemaHandlerHandleTest extends ApplySchemaHandlerTest
{
    public function testHandleReturnsAppliedAndSkippedKeys(): void
    {
        $result = $this->class->handle($this->emptyCommand());

        $this->assertArrayHasKey('applied', $result);
        $this->assertArrayHasKey('skipped', $result);
    }

    public function testHandleReturnsEmptyListsWhenSnapshotsAreIdentical(): void
    {
        // Both current and target are empty → no diff → nothing to apply
        $result = $this->class->handle($this->emptyCommand());

        $this->assertSame([], $result['applied']);
        $this->assertSame([], $result['skipped']);
    }

    // ── Collections ────────────────────────────────────────────────────────

    public function testHandleCreatesCollectionAndLogsApplied(): void
    {
        // Current: empty; Target: 'articles' collection
        // createCollectionHandler needs findByName → null (not yet existing)
        $this->collectionRepository->method('findByName')->willReturn(null);

        $result = $this->buildHandler()->handle($this->commandWithCollection('articles'));

        $this->assertContains('create_collection:articles', $result['applied']);
    }

    public function testHandleSkipsCollectionWhenItAlreadyExistsInDatabase(): void
    {
        // CreateCollectionHandler calls findByName → existing entity → throws → skipped
        $existing = new CollectionMeta('articles');
        $this->collectionRepository->method('findByName')->willReturn($existing);

        $result = $this->buildHandler()->handle($this->commandWithCollection('articles'));

        $this->assertContains('create_collection:articles', $result['skipped']);
        $this->assertNotContains('create_collection:articles', $result['applied']);
    }

    public function testHandleDoesNotDeleteCollectionWhenForceIsFalse(): void
    {
        // Current: 'articles' exists; Target: empty → delete diff, but force=false
        $existing = new CollectionMeta('articles');
        $this->collectionReturn = [$existing];

        $result = $this->buildHandler()->handle($this->emptyCommand(force: false));

        $this->assertNotContains('delete_collection:articles', $result['applied']);
    }

    public function testHandleDeletesCollectionWhenForceIsTrue(): void
    {
        // Current: 'articles' exists; Target: empty → delete diff, and force=true
        $existing = new CollectionMeta('articles');
        $this->collectionReturn = [$existing];
        $this->collectionRepository->method('findByName')->willReturn($existing);

        $result = $this->buildHandler()->handle($this->emptyCommand(force: true));

        $this->assertContains('delete_collection:articles', $result['applied']);
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function testHandleCreatesRelationAndLogsApplied(): void
    {
        // Current: no relations; Target: articles.author_id relation
        $this->relationRepository->method('findByCollectionAndField')->willReturn(null);

        $result = $this->buildHandler()->handle($this->commandWithRelation('articles', 'author_id'));

        $this->assertContains('create_relation:articles.author_id', $result['applied']);
    }

    public function testHandleSkipsRelationWhenItAlreadyExists(): void
    {
        // CreateRelationHandler calls findByCollectionAndField → existing → throws → skipped
        $existing = new Relation('articles', 'author_id');
        $this->relationRepository->method('findByCollectionAndField')->willReturn($existing);

        $result = $this->buildHandler()->handle($this->commandWithRelation('articles', 'author_id'));

        $this->assertContains('create_relation:articles.author_id', $result['skipped']);
    }

    public function testHandleDeletesRelationWhenForceIsTrue(): void
    {
        // Current: relation exists; Target: no relations → delete diff, force=true
        $existing = new Relation('articles', 'author_id');
        $this->relationReturn = [$existing];
        $this->relationRepository->method('findByCollectionAndField')->willReturn($existing);

        $result = $this->buildHandler()->handle($this->emptyCommand(force: true));

        $this->assertContains('delete_relation:articles.author_id', $result['applied']);
    }

    public function testHandleDoesNotDeleteRelationWhenForceIsFalse(): void
    {
        // Current: relation exists; Target: no relations → force=false → not deleted
        $existing = new Relation('articles', 'author_id');
        $this->relationReturn = [$existing];

        $result = $this->buildHandler()->handle($this->emptyCommand(force: false));

        $this->assertNotContains('delete_relation:articles.author_id', $result['applied']);
    }
}
