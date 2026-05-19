<?php

/**
 * @file VersionReconstituteTest.php
 *
 * Tests for Version::reconstitute().
 *
 * @package App\Versions\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Domain\Entity\Tests;

use App\Versions\Domain\Entity\Version;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that reconstitute() bypasses the auto-generated id and timestamps
 * and correctly populates every field from the provided arguments.
 */
#[CoversClass(className: Version::class)]
#[CoversMethod(Version::class, 'reconstitute')]
final class VersionReconstituteTest extends VersionTest
{
    /**
     * UUID used as the lookup identifier in all get() test scenarios.
     * @var string
     */
    private const string FIXED_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * Test that reconstitute() sets the id to the provided value, not a generated one.
     */
    public function testReconstituteOverridesGeneratedId(): void
    {
        $version = $this->makeReconstitutedVersion();

        $this->assertSame(self::FIXED_UUID, $version->getId());
    }

    /**
     * Test that reconstitute() sets the collection field correctly.
     */
    public function testReconstitutesSetsCollection(): void
    {
        $version = $this->makeReconstitutedVersion();

        $this->assertSame('articles', $version->getCollection());
    }

    /**
     * Test that reconstitute() sets the item field correctly.
     */
    public function testReconstituteSetsItem(): void
    {
        $version = $this->makeReconstitutedVersion();

        $this->assertSame('item-uuid-1', $version->getItem());
    }

    /**
     * Test that reconstitute() sets the key field correctly.
     */
    public function testReconstituteSetsKey(): void
    {
        $version = $this->makeReconstitutedVersion();

        $this->assertSame('main', $version->getKey());
    }

    /**
     * Test that reconstitute() sets the data field correctly.
     */
    public function testReconstituteSetsData(): void
    {
        $version = $this->makeReconstitutedVersion();

        $this->assertSame(['title' => 'Hello'], $version->getData());
    }

    /**
     * Test that reconstitute() sets delta to null when null is provided.
     */
    public function testReconstituteSetsDeltaToNull(): void
    {
        $version = $this->makeReconstitutedVersion();

        $this->assertNull($version->getDelta());
    }

    /**
     * Test that reconstitute() sets the createdAt to the provided timestamp, not the current time.
     */
    public function testReconstituteOverridesGeneratedCreatedAt(): void
    {
        $expected = new DateTimeImmutable('2024-01-01T00:00:00+00:00');
        $version  = $this->makeReconstitutedVersion();

        $this->assertSame($expected->getTimestamp(), $version->getCreatedAt()->getTimestamp());
    }

    /**
     * Test that reconstitute() sets updatedAt to null when null is provided.
     */
    public function testReconstitutePreservesNullUpdatedAt(): void
    {
        $version = $this->makeReconstitutedVersion();

        $this->assertNull($version->getUpdatedAt());
    }

    /**
     * Test that reconstitute() sets a non-null updatedAt when provided.
     */
    public function testReconstituteSetsUpdatedAtWhenProvided(): void
    {
        $updatedAt = new DateTimeImmutable('2024-06-01T12:00:00+00:00');

        $version = Version::reconstitute(
            id:         self::FIXED_UUID,
            collection: 'articles',
            item:       'item-uuid-1',
            key:        'main',
            data:       [],
            delta:      null,
            userId:     null,
            createdAt:  new DateTimeImmutable('2024-01-01T00:00:00+00:00'),
            updatedAt:  $updatedAt,
        );

        $this->assertSame($updatedAt->getTimestamp(), $version->getUpdatedAt()->getTimestamp());
    }
}
