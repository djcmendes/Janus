<?php

/**
 * @file ActivityRepositoryBaseTest.php
 *
 * Tests for ActivityRepository construction and interface compliance.
 *
 * @package App\Activity\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Repository\Tests;

use App\Activity\Domain\Repository\ActivityRepositoryInterface;
use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use App\Activity\Infrastructure\Repository\ActivityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that ActivityRepository is wired correctly after construction:
 * implements the domain interface, extends the Doctrine base, and is
 * configured for the ActivityEntity persistence model.
 */
#[CoversClass(className:  ActivityRepository::class)]
final class ActivityRepositoryBaseTest extends ActivityRepositoryTest
{
    /**
     * Test that the repository implements the domain ActivityRepositoryInterface.
     */
    public function testImplementsActivityRepositoryInterface(): void
    {
        $this->assertInstanceOf(expected: ActivityRepositoryInterface::class, actual: $this->class);
    }

    /**
     * Test that the repository extends Doctrine's ServiceEntityRepository.
     */
    public function testExtendsServiceEntityRepository(): void
    {
        $this->assertInstanceOf(expected: ServiceEntityRepository::class, actual: $this->class);
    }

    /**
     * Test that the repository is bound to the ActivityEntity persistence model.
     */
    public function testIsConfiguredForActivityEntity(): void
    {
        $this->assertSame(expected: ActivityEntity::class, actual: $this->classMetadata->name);
    }
}
