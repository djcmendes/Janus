<?php

/**
 * @file CollectionMetaRepositoryBaseTest.php
 *
 * Interface compliance tests for CollectionMetaRepository.
 *
 * @package App\Collections\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Repository\Tests;

use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Collections\Infrastructure\Repository\CollectionMetaRepository;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CollectionMetaRepository::class)]
final class CollectionMetaRepositoryBaseTest extends CollectionMetaRepositoryTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testImplementsCollectionMetaRepositoryInterface(): void
    {
        $this->assertInstanceOf(CollectionMetaRepositoryInterface::class, $this->class);
    }

    public function testIsInstanceOfCollectionMetaRepository(): void
    {
        $this->assertInstanceOf(CollectionMetaRepository::class, $this->class);
    }
}
