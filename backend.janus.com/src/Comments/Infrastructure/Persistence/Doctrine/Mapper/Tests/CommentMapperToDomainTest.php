<?php

/**
 * @file CommentMapperToDomainTest.php
 *
 * Tests for CommentMapper::toDomain().
 *
 * @package App\Comments\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Comments\Domain\Entity\Comment;
use App\Comments\Infrastructure\Persistence\Doctrine\Mapper\CommentMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for CommentMapper::toDomain() — verifying field mapping from persistence to domain.
 */
#[CoversClass(CommentMapper::class)]
#[CoversMethod(CommentMapper::class, 'toDomain')]
final class CommentMapperToDomainTest extends CommentMapperTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that toDomain() returns a domain Comment instance.
     */
    public function testToDomainReturnsDomainComment(): void
    {
        $this->assertInstanceOf(Comment::class, $this->class->toDomain($this->makeEntity()));
    }

    /**
     * Test that toDomain() maps the id correctly.
     */
    public function testToDomainMapsId(): void
    {
        $this->assertSame(self::FIXED_UUID, $this->class->toDomain($this->makeEntity())->getId());
    }

    /**
     * Test that toDomain() maps the collection correctly.
     */
    public function testToDomainMapsCollection(): void
    {
        $this->assertSame('posts', $this->class->toDomain($this->makeEntity())->getCollection());
    }

    /**
     * Test that toDomain() maps the item correctly.
     */
    public function testToDomainMapsItem(): void
    {
        $this->assertSame('42', $this->class->toDomain($this->makeEntity())->getItem());
    }

    /**
     * Test that toDomain() maps the comment text correctly.
     */
    public function testToDomainMapsComment(): void
    {
        $this->assertSame('Hello world', $this->class->toDomain($this->makeEntity())->getComment());
    }

    /**
     * Test that toDomain() maps the userId correctly.
     */
    public function testToDomainMapsUserId(): void
    {
        $this->assertSame(
            'bbbbbbbb-0000-7000-8000-000000000002',
            $this->class->toDomain($this->makeEntity())->getUserId(),
        );
    }

    /**
     * Test that toDomain() maps the createdAt timestamp correctly.
     */
    public function testToDomainMapsCreatedAt(): void
    {
        $ts     = new \DateTimeImmutable('2024-01-01T00:00:00+00:00');
        $entity = $this->makeEntity()->setCreatedAt($ts);

        $this->assertEquals($ts, $this->class->toDomain($entity)->getCreatedAt());
    }

    /**
     * Test that toDomain() maps updatedAt as null when not set.
     */
    public function testToDomainMapsNullUpdatedAt(): void
    {
        $this->assertNull($this->class->toDomain($this->makeEntity())->getUpdatedAt());
    }

    /**
     * Test that toDomain() maps updatedAt correctly when set.
     */
    public function testToDomainMapsUpdatedAt(): void
    {
        $ts     = new \DateTimeImmutable('2024-06-01T00:00:00+00:00');
        $entity = $this->makeEntity()->setUpdatedAt($ts);

        $this->assertEquals($ts, $this->class->toDomain($entity)->getUpdatedAt());
    }

    // Roundtrip ────────────────────────────────────────────────────

    /**
     * Test that a roundtrip toDomain(toPersistence($domain)) preserves the id.
     */
    public function testRoundtripPreservesId(): void
    {
        $domain = $this->makeDomain();
        $result = $this->class->toDomain($this->class->toPersistence($domain));

        $this->assertSame($domain->getId(), $result->getId());
    }

    /**
     * Test that a roundtrip toDomain(toPersistence($domain)) preserves the comment text.
     */
    public function testRoundtripPreservesComment(): void
    {
        $domain = $this->makeDomain();
        $result = $this->class->toDomain($this->class->toPersistence($domain));

        $this->assertSame($domain->getComment(), $result->getComment());
    }

    /**
     * Test that a roundtrip toDomain(toPersistence($domain)) preserves the userId.
     */
    public function testRoundtripPreservesUserId(): void
    {
        $domain = $this->makeDomain();
        $result = $this->class->toDomain($this->class->toPersistence($domain));

        $this->assertSame($domain->getUserId(), $result->getUserId());
    }
}
