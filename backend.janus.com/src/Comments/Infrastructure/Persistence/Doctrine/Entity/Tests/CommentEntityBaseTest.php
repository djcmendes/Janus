<?php

/**
 * @file CommentEntityBaseTest.php
 *
 * Setter, getter, and fluent-interface compliance tests for CommentEntity.
 *
 * @package App\Comments\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Comments\Infrastructure\Persistence\Doctrine\Entity\CommentEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Uid\Uuid;

/**
 * Verifies that all CommentEntity setters store their values and return
 * the same instance (fluent interface), and that getters return the correct types.
 */
#[CoversClass(CommentEntity::class)]
final class CommentEntityBaseTest extends CommentEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that setId() stores the UUID and getById() returns it.
     */
    public function testSetIdStoresAndGetIdReturns(): void
    {
        $uuid = Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001');
        $this->class->setId($uuid);

        $this->assertSame($uuid, $this->class->getId());
    }

    /**
     * Test that setId() returns the same instance (fluent interface).
     */
    public function testSetIdReturnsSelf(): void
    {
        $result = $this->class->setId(Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001'));

        $this->assertSame($this->class, $result);
    }

    /**
     * Test that setCollection() stores the value and getCollection() returns it.
     */
    public function testSetCollectionStoresAndGetCollectionReturns(): void
    {
        $this->class->setCollection('articles');

        $this->assertSame('articles', $this->class->getCollection());
    }

    /**
     * Test that setCollection() returns the same instance (fluent interface).
     */
    public function testSetCollectionReturnsSelf(): void
    {
        $result = $this->class->setCollection('articles');

        $this->assertSame($this->class, $result);
    }

    /**
     * Test that setItem() stores the value and getItem() returns it.
     */
    public function testSetItemStoresAndGetItemReturns(): void
    {
        $this->class->setItem('99');

        $this->assertSame('99', $this->class->getItem());
    }

    /**
     * Test that setItem() returns the same instance (fluent interface).
     */
    public function testSetItemReturnsSelf(): void
    {
        $result = $this->class->setItem('99');

        $this->assertSame($this->class, $result);
    }

    /**
     * Test that setComment() stores the value and getComment() returns it.
     */
    public function testSetCommentStoresAndGetCommentReturns(): void
    {
        $this->class->setComment('Test comment');

        $this->assertSame('Test comment', $this->class->getComment());
    }

    /**
     * Test that setComment() returns the same instance (fluent interface).
     */
    public function testSetCommentReturnsSelf(): void
    {
        $result = $this->class->setComment('Test comment');

        $this->assertSame($this->class, $result);
    }

    /**
     * Test that setUserId() stores the value and getUserId() returns it.
     */
    public function testSetUserIdStoresAndGetUserIdReturns(): void
    {
        $this->class->setUserId('bbbbbbbb-0000-7000-8000-000000000002');

        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $this->class->getUserId());
    }

    /**
     * Test that setUserId() returns the same instance (fluent interface).
     */
    public function testSetUserIdReturnsSelf(): void
    {
        $result = $this->class->setUserId('bbbbbbbb-0000-7000-8000-000000000002');

        $this->assertSame($this->class, $result);
    }

    /**
     * Test that setCreatedAt() stores the value and getCreatedAt() returns it.
     */
    public function testSetCreatedAtStoresAndGetCreatedAtReturns(): void
    {
        $ts = new \DateTimeImmutable('2024-01-01T00:00:00+00:00');
        $this->class->setCreatedAt($ts);

        $this->assertSame($ts, $this->class->getCreatedAt());
    }

    /**
     * Test that setCreatedAt() returns the same instance (fluent interface).
     */
    public function testSetCreatedAtReturnsSelf(): void
    {
        $result = $this->class->setCreatedAt(new \DateTimeImmutable());

        $this->assertSame($this->class, $result);
    }

    /**
     * Test that setUpdatedAt() stores a non-null value and getUpdatedAt() returns it.
     */
    public function testSetUpdatedAtStoresNonNullValue(): void
    {
        $ts = new \DateTimeImmutable('2024-06-01T00:00:00+00:00');
        $this->class->setUpdatedAt($ts);

        $this->assertSame($ts, $this->class->getUpdatedAt());
    }

    /**
     * Test that setUpdatedAt() accepts null and getUpdatedAt() returns null.
     */
    public function testSetUpdatedAtAcceptsNull(): void
    {
        $this->class->setUpdatedAt(null);

        $this->assertNull($this->class->getUpdatedAt());
    }

    /**
     * Test that setUpdatedAt() returns the same instance (fluent interface).
     */
    public function testSetUpdatedAtReturnsSelf(): void
    {
        $result = $this->class->setUpdatedAt(null);

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    /**
     * Test that a fully-populated entity via makeEntity() has all values accessible.
     */
    public function testMakeEntityProducesFullyPopulatedInstance(): void
    {
        $entity = $this->makeEntity();

        $this->assertSame('posts', $entity->getCollection());
        $this->assertSame('42', $entity->getItem());
        $this->assertSame('Hello world', $entity->getComment());
        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $entity->getUserId());
        $this->assertNull($entity->getUpdatedAt());
    }
}
