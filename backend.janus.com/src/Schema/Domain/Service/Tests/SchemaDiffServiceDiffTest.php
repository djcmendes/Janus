<?php

/**
 * @file SchemaDiffServiceDiffTest.php
 *
 * Tests for SchemaDiffService::diff().
 *
 * @package App\Schema\Domain\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Domain\Service\Tests;

use App\Schema\Domain\Service\SchemaDiffService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies diff() correctly identifies creates, updates, and deletes for
 * collections, fields, and relations.
 */
#[CoversClass(className: SchemaDiffService::class)]
#[CoversMethod(SchemaDiffService::class, 'diff')]
final class SchemaDiffServiceDiffTest extends SchemaDiffServiceTest
{
    // ── Collections ────────────────────────────────────────────────────────

    public function testIdenticalSnapshotsProduceEmptyDiff(): void
    {
        $snap   = $this->snapshotWithCollection('articles');
        $result = $this->class->diff($snap, $snap);

        $this->assertEmpty($result['collections']['create']);
        $this->assertEmpty($result['collections']['update']);
        $this->assertEmpty($result['collections']['delete']);
    }

    public function testNewCollectionInTargetAppearsInCreate(): void
    {
        $current = $this->emptySnapshot();
        $target  = $this->snapshotWithCollection('articles');

        $result = $this->class->diff($current, $target);

        $this->assertCount(1, $result['collections']['create']);
        $this->assertSame('articles', $result['collections']['create'][0]['collection']);
    }

    public function testCollectionMissingFromTargetAppearsInDelete(): void
    {
        $current = $this->snapshotWithCollection('articles');
        $target  = $this->emptySnapshot();

        $result = $this->class->diff($current, $target);

        $this->assertContains('articles', $result['collections']['delete']);
    }

    public function testChangedCollectionMetaAppearsInUpdate(): void
    {
        $current = $this->snapshotWithCollection('articles', ['label' => 'Old Label']);
        $target  = $this->snapshotWithCollection('articles', ['label' => 'New Label']);

        $result = $this->class->diff($current, $target);

        $this->assertCount(1, $result['collections']['update']);
        $this->assertSame('articles', $result['collections']['update'][0]['collection']);
        $this->assertSame('New Label', $result['collections']['update'][0]['diff']['label']);
    }

    public function testUnchangedCollectionDoesNotAppearInUpdate(): void
    {
        $snap   = $this->snapshotWithCollection('articles', ['label' => 'Same']);
        $result = $this->class->diff($snap, $snap);

        $this->assertEmpty($result['collections']['update']);
    }

    // ── Fields ─────────────────────────────────────────────────────────────

    public function testNewFieldInTargetAppearsInFieldsCreate(): void
    {
        $current = $this->snapshotWithCollection('articles');
        $target  = $this->snapshotWithField('articles', 'title', 'string');

        $result = $this->class->diff($current, $target);

        $this->assertCount(1, $result['fields']['create']);
        $this->assertSame('title', $result['fields']['create'][0]['field']);
        $this->assertSame('articles', $result['fields']['create'][0]['collection']);
    }

    public function testFieldMissingFromTargetAppearsInFieldsDelete(): void
    {
        $current = $this->snapshotWithField('articles', 'title', 'string');
        $target  = $this->snapshotWithCollection('articles');

        $result = $this->class->diff($current, $target);

        $this->assertCount(1, $result['fields']['delete']);
        $this->assertSame('title', $result['fields']['delete'][0]['field']);
    }

    public function testChangedFieldMetaAppearsInFieldsUpdate(): void
    {
        $current = $this->snapshotWithField('articles', 'title', 'string', ['label' => 'Old']);
        $target  = $this->snapshotWithField('articles', 'title', 'string', ['label' => 'New']);

        $result = $this->class->diff($current, $target);

        $this->assertCount(1, $result['fields']['update']);
        $this->assertSame('title', $result['fields']['update'][0]['field']);
        $this->assertSame('New', $result['fields']['update'][0]['diff']['meta']['label']);
    }

    public function testChangedFieldTypeAppearsInFieldsUpdate(): void
    {
        $current = $this->snapshotWithField('articles', 'count', 'integer');
        $target  = $this->snapshotWithField('articles', 'count', 'bigInteger');

        $result = $this->class->diff($current, $target);

        $this->assertCount(1, $result['fields']['update']);
        $this->assertSame('bigInteger', $result['fields']['update'][0]['diff']['type']);
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function testNewRelationInTargetAppearsInRelationsCreate(): void
    {
        $current = $this->emptySnapshot();
        $target  = $this->snapshotWithRelation('articles', 'author_id', 'users');

        $result = $this->class->diff($current, $target);

        $this->assertCount(1, $result['relations']['create']);
        $this->assertSame('articles', $result['relations']['create'][0]['many_collection']);
    }

    public function testRelationMissingFromTargetAppearsInRelationsDelete(): void
    {
        $current = $this->snapshotWithRelation('articles', 'author_id', 'users');
        $target  = $this->emptySnapshot();

        $result = $this->class->diff($current, $target);

        $this->assertCount(1, $result['relations']['delete']);
        $this->assertSame('articles', $result['relations']['delete'][0]['many_collection']);
        $this->assertSame('author_id', $result['relations']['delete'][0]['many_field']);
    }

    public function testIdenticalRelationsProduceEmptyDiff(): void
    {
        $snap   = $this->snapshotWithRelation('articles', 'author_id', 'users');
        $result = $this->class->diff($snap, $snap);

        $this->assertEmpty($result['relations']['create']);
        $this->assertEmpty($result['relations']['update']);
        $this->assertEmpty($result['relations']['delete']);
    }
}
