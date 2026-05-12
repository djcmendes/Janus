<?php

/**
 * @file VersionRepositoryFindByCollectionItemAndKeyTest.php
 *
 * Tests for VersionRepository::findByCollectionItemAndKey().
 *
 * @package App\Versions\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Repository\Tests;

use App\Versions\Domain\Entity\Version;
use App\Versions\Infrastructure\Repository\VersionRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies findByCollectionItemAndKey() returns a mapped domain Version or null.
 */
#[CoversClass(VersionRepository::class)]
#[CoversMethod(VersionRepository::class, 'findByCollectionItemAndKey')]
final class VersionRepositoryFindByCollectionItemAndKeyTest extends VersionRepositoryTest
{
    /**
     * Test that findByCollectionItemAndKey() returns a domain Version when the record exists.
     */
    public function testReturnsDomainVersionWhenFound(): void
    {
        $this->persister->method('load')->willReturn($this->makeVersionEntity());

        $result = $this->class->findByCollectionItemAndKey('articles', 'item-uuid-1', 'main');

        $this->assertInstanceOf(Version::class, $result);
    }

    /**
     * Test that findByCollectionItemAndKey() returns null when no matching record exists.
     */
    public function testReturnsNullWhenNotFound(): void
    {
        $this->persister->method('load')->willReturn(null);

        $result = $this->class->findByCollectionItemAndKey('articles', 'item-uuid-1', 'nonexistent');

        $this->assertNull($result);
    }
}
