<?php

/**
 * @file VersionRepositoryBaseTest.php
 *
 * Tests for VersionRepository construction and interface compliance.
 *
 * @package App\Versions\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Repository\Tests;

use App\Versions\Domain\Repository\VersionRepositoryInterface;
use App\Versions\Infrastructure\Persistence\Doctrine\Entity\VersionEntity;
use App\Versions\Infrastructure\Repository\VersionRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that VersionRepository is wired correctly: implements the domain interface,
 * extends the Doctrine base, and is configured for the VersionEntity persistence model.
 */
#[CoversClass(className: VersionRepository::class)]
final class VersionRepositoryBaseTest extends VersionRepositoryTest
{
    public function testImplementsVersionRepositoryInterface(): void
    {
        $this->assertInstanceOf(VersionRepositoryInterface::class, $this->class);
    }

    public function testExtendsServiceEntityRepository(): void
    {
        $this->assertInstanceOf(ServiceEntityRepository::class, $this->class);
    }

    public function testIsConfiguredForVersionEntity(): void
    {
        $this->assertSame(VersionEntity::class, $this->classMetadata->name);
    }
}
