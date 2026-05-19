<?php

/**
 * @file VersionRepositoryFindByIdTest.php
 *
 * Tests for VersionRepository::findById().
 *
 * @package App\Versions\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Repository\Tests;

use App\Versions\Domain\Entity\Version;
use App\Versions\Infrastructure\Persistence\Doctrine\Entity\VersionEntity;
use App\Versions\Infrastructure\Repository\VersionRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies findById() returns a mapped domain Version or null.
 */
#[CoversClass(className: VersionRepository::class)]
#[CoversMethod(VersionRepository::class, 'findById')]
final class VersionRepositoryFindByIdTest extends VersionRepositoryTest
{
    /** @var string */
    private const string LOOKUP_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * Test that findById() returns a domain Version when the record exists.
     */
    public function testFindByIdReturnsDomainVersionForExistingId(): void
    {
        $this->entityManager->method('find')->willReturn($this->makeVersionEntity());

        $result = $this->class->findById(self::LOOKUP_UUID);

        $this->assertInstanceOf(Version::class, $result);
    }

    /**
     * Test that findById() maps the VersionEntity collection onto the domain Version.
     */
    public function testFindByIdMapsEntityToDomainVersion(): void
    {
        $this->entityManager->method('find')
            ->willReturn($this->makeVersionEntity());

        $result = $this->class->findById(self::LOOKUP_UUID);

        $this->assertSame('articles', $result->getCollection());
        $this->assertSame('main', $result->getKey());
    }

    /**
     * Test that findById() returns null when no record exists.
     */
    public function testFindByIdReturnsNullForNonExistentId(): void
    {
        $this->entityManager->method('find')->willReturn(null);

        $result = $this->class->findById(self::LOOKUP_UUID);

        $this->assertNull($result);
    }

    /**
     * Test that findById() passes VersionEntity::class and the UUID to the entity manager.
     */
    public function testFindByIdPassesCorrectClassAndIdToEntityManager(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('find')
            ->with(VersionEntity::class, self::LOOKUP_UUID)
            ->willReturn(null);

        $this->class->findById(self::LOOKUP_UUID);
    }
}
