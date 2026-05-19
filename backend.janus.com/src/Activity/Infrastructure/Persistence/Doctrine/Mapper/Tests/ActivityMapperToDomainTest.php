<?php

/**
 * @file ActivityMapperToDomainTest.php
 *
 * Tests for ActivityMapper::toDomain().
 *
 * @package App\Activity\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Activity\Domain\Entity\Activity;
use App\Activity\Infrastructure\Persistence\Doctrine\Mapper\ActivityMapper;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className:  ActivityMapper::class)]
#[CoversMethod(className: ActivityMapper::class, methodName: 'toDomain')]
final class ActivityMapperToDomainTest extends ActivityMapperTest
{
    /**
     * Test that toDomain() returns an instance of the domain Activity class.
     */
    public function testToDomainReturnsDomainActivity(): void
    {
        $this->assertInstanceOf(
            expected: Activity::class,
            actual:   $this->class->toDomain(entity: $this->makeEntity())
        );
    }

    /**
     * Test that toDomain() maps the entity id to the domain Activity.
     */
    public function testToDomainMapsId(): void
    {
        $this->assertSame(
            expected: self::FIXED_UUID,
            actual:   $this->class->toDomain(entity: $this->makeEntity())->id,
        );
    }

    /**
     * Test that toDomain() maps the action field to the domain Activity.
     */
    public function testToDomainMapsAction(): void
    {
        $this->assertSame(
            expected: 'create',
            actual:   $this->class->toDomain(entity: $this->makeEntity())->action
        );
    }

    /**
     * Test that toDomain() maps the collection field to the domain Activity.
     */
    public function testToDomainMapsCollection(): void
    {
        $this->assertSame(
            expected: 'posts',
            actual:   $this->class->toDomain(entity: $this->makeEntity())->collection
        );
    }

    /**
     * Test that toDomain() maps the item field to the domain Activity.
     */
    public function testToDomainMapsItem(): void
    {
        $this->assertSame(
            expected: '42',
            actual:   $this->class->toDomain(entity: $this->makeEntity())->item
        );
    }

    /**
     * Test that toDomain() maps the userId field to the domain Activity.
     */
    public function testToDomainMapsUserId(): void
    {
        $this->assertSame(
            expected: 'bbbbbbbb-0000-7000-8000-000000000002',
            actual:   $this->class->toDomain(entity: $this->makeEntity())->userId,
        );
    }

    /**
     * Test that toDomain() maps the ip field to the domain Activity.
     */
    public function testToDomainMapsIp(): void
    {
        $this->assertSame(
            expected: '127.0.0.1',
            actual:   $this->class->toDomain(entity: $this->makeEntity())->ip
        );
    }

    /**
     * Test that toDomain() maps the userAgent field to the domain Activity.
     */
    public function testToDomainMapsUserAgent(): void
    {
        $this->assertSame(
            expected: 'PHPUnit',
            actual:   $this->class->toDomain(entity: $this->makeEntity())->userAgent
        );
    }

    /**
     * Test that toDomain() maps the timestamp field to the domain Activity.
     */
    public function testToDomainMapsTimestamp(): void
    {
        $ts     = new DateTimeImmutable(datetime: '2024-01-01T00:00:00+00:00');
        $entity = $this->makeEntity()
                       ->setTimestamp(timestamp: $ts);

        $this->assertEquals(
            expected: $ts,
            actual:   $this->class->toDomain(entity: $entity)->timestamp,
        );
    }

    /**
     * Test that toDomain() maps all nullable fields to null when they are not set on the entity.
     */
    public function testToDomainHandlesNullOptionalFields(): void
    {
        $entity = $this->makeEntity()
                       ->setCollection(collection: null)
                       ->setItem(item: null)
                       ->setUserId(userId: null)
                       ->setIp(ip: null)
                       ->setUserAgent(userAgent: null);

        $domain = $this->class->toDomain($entity);

        $this->assertNull(actual: $domain->collection);
        $this->assertNull(actual: $domain->item);
        $this->assertNull(actual: $domain->userId);
        $this->assertNull(actual: $domain->ip);
        $this->assertNull(actual: $domain->userAgent);
    }

    /**
     * Test that a toPersistence() → toDomain() roundtrip preserves the id.
     */
    public function testRoundtripPreservesId(): void
    {
        $domain = $this->makeDomain();
        $result = $this->class->toDomain(
            entity: $this->class->toPersistence(domain: $domain)
        );

        $this->assertSame(
            expected: $domain->id,
            actual: $result->id
        );
    }

    /**
     * Test that a toPersistence() → toDomain() roundtrip preserves the action.
     */
    public function testRoundtripPreservesAction(): void
    {
        $domain = $this->makeDomain();
        $result = $this->class->toDomain(
            entity: $this->class->toPersistence(domain: $domain)
        );

        $this->assertSame(
            expected: $domain->action,
            actual:   $result->action
        );
    }

    /**
     * Test that a toPersistence() → toDomain() roundtrip preserves the userId.
     */
    public function testRoundtripPreservesUserId(): void
    {
        $domain = $this->makeDomain();
        $result = $this->class->toDomain(
            entity: $this->class->toPersistence(domain: $domain)
        );

        $this->assertSame(
            expected: $domain->userId,
            actual:   $result->userId
        );
    }
}
