<?php

/**
 * @file CommentRepositoryBaseTest.php
 *
 * Tests for CommentRepository construction and interface compliance.
 *
 * @package App\Comments\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Infrastructure\Repository\Tests;

use App\Comments\Domain\Repository\CommentRepositoryInterface;
use App\Comments\Infrastructure\Persistence\Doctrine\Entity\CommentEntity;
use App\Comments\Infrastructure\Repository\CommentRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that CommentRepository is wired correctly after construction:
 * implements the domain interface, extends the Doctrine base, and is
 * configured for the CommentEntity persistence model.
 */
#[CoversClass(CommentRepository::class)]
final class CommentRepositoryBaseTest extends CommentRepositoryTest
{
    /**
     * Test that the repository implements the domain CommentRepositoryInterface.
     */
    public function testImplementsCommentRepositoryInterface(): void
    {
        $this->assertInstanceOf(CommentRepositoryInterface::class, $this->class);
    }

    /**
     * Test that the repository extends Doctrine's ServiceEntityRepository.
     */
    public function testExtendsServiceEntityRepository(): void
    {
        $this->assertInstanceOf(ServiceEntityRepository::class, $this->class);
    }

    /**
     * Test that the repository is bound to the CommentEntity persistence model.
     */
    public function testIsConfiguredForCommentEntity(): void
    {
        $this->assertSame(CommentEntity::class, $this->classMetadata->name);
    }
}
