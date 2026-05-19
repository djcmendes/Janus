<?php

/**
 * @file CollectionMetaRepositoryFindByNameTest.php
 *
 * Tests for CollectionMetaRepository::findByName().
 *
 * @package App\Collections\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Repository\Tests;

use App\Collections\Domain\Entity\CollectionMeta;
use App\Collections\Infrastructure\Repository\CollectionMetaRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className: CollectionMetaRepository::class)]
#[CoversMethod(CollectionMetaRepository::class, 'findByName')]
final class CollectionMetaRepositoryFindByNameTest extends CollectionMetaRepositoryTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testFindByNameReturnsDomainEntityWhenFound(): void
    {
        $entity = $this->makeCollectionMetaEntity();

        $this->persister->method('load')->willReturn($entity);

        $result = $this->class->findByName('articles');

        $this->assertInstanceOf(CollectionMeta::class, $result);
    }

    public function testFindByNameMapsNameFieldCorrectly(): void
    {
        $entity = $this->makeCollectionMetaEntity();

        $this->persister->method('load')->willReturn($entity);

        $result = $this->class->findByName('articles');

        $this->assertSame('articles', $result->getName());
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testFindByNameReturnsNullWhenNotFound(): void
    {
        $this->persister->method('load')->willReturn(null);

        $result = $this->class->findByName('nonexistent');

        $this->assertNull($result);
    }
}
