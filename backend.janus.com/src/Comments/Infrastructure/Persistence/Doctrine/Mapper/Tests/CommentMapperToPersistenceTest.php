<?php

/**
 * @file CommentMapperToPersistenceTest.php
 *
 * Tests for CommentMapper::toPersistence().
 *
 * @package App\Comments\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Comments\Infrastructure\Persistence\Doctrine\Entity\CommentEntity;
use App\Comments\Infrastructure\Persistence\Doctrine\Mapper\CommentMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\Uid\Uuid;

/**
 * Tests for CommentMapper::toPersistence() — verifying field mapping from domain to persistence.
 */
#[CoversClass(CommentMapper::class)]
#[CoversMethod(CommentMapper::class, 'toPersistence')]
final class CommentMapperToPersistenceTest extends CommentMapperTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that toPersistence() returns a CommentEntity instance.
     */
    public function testToPersistenceReturnsCommentEntity(): void
    {
        $this->assertInstanceOf(CommentEntity::class, $this->class->toPersistence($this->makeDomain()));
    }

    /**
     * Test that toPersistence() maps the id as a Uuid value object.
     */
    public function testToPersistenceMapsIdAsUuid(): void
    {
        $domain = $this->makeDomain();
        $entity = $this->class->toPersistence($domain);

        $this->assertInstanceOf(Uuid::class, $entity->getId());
        $this->assertSame($domain->getId(), (string) $entity->getId());
    }

    /**
     * Test that toPersistence() maps the collection correctly.
     */
    public function testToPersistenceMapsCollection(): void
    {
        $entity = $this->class->toPersistence($this->makeDomain());

        $this->assertSame('posts', $entity->getCollection());
    }

    /**
     * Test that toPersistence() maps the item correctly.
     */
    public function testToPersistenceMapsItem(): void
    {
        $entity = $this->class->toPersistence($this->makeDomain());

        $this->assertSame('42', $entity->getItem());
    }

    /**
     * Test that toPersistence() maps the comment text correctly.
     */
    public function testToPersistenceMapsComment(): void
    {
        $entity = $this->class->toPersistence($this->makeDomain());

        $this->assertSame('Hello world', $entity->getComment());
    }

    /**
     * Test that toPersistence() maps the userId correctly.
     */
    public function testToPersistenceMapsUserId(): void
    {
        $entity = $this->class->toPersistence($this->makeDomain());

        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $entity->getUserId());
    }

    /**
     * Test that toPersistence() maps updatedAt as null when not set on the domain entity.
     */
    public function testToPersistenceMapsNullUpdatedAt(): void
    {
        $entity = $this->class->toPersistence($this->makeDomain());

        $this->assertNull($entity->getUpdatedAt());
    }

    /**
     * Test that toPersistence() maps updatedAt correctly when set on the domain entity.
     */
    public function testToPersistenceMapsUpdatedAtWhenSet(): void
    {
        $domain = $this->makeDomain();
        $domain->setComment('Edited');

        $entity = $this->class->toPersistence($domain);

        $this->assertNotNull($entity->getUpdatedAt());
        $this->assertEquals($domain->getUpdatedAt(), $entity->getUpdatedAt());
    }
}
