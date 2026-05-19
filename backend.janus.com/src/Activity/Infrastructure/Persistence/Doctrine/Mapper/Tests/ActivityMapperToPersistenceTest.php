<?php

/**
 * @file ActivityMapperToPersistenceTest.php
 *
 * Tests for ActivityMapper::toPersistence().
 *
 * @package App\Activity\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use App\Activity\Infrastructure\Persistence\Doctrine\Mapper\ActivityMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\Uid\Uuid;

#[CoversClass(className:  ActivityMapper::class)]
#[CoversMethod(className: ActivityMapper::class, methodName: 'toPersistence')]
final class ActivityMapperToPersistenceTest extends ActivityMapperTest
{
    /**
     * Test that toPersistence() returns an instance of the ActivityEntity persistence model.
     */
    public function testToPersistenceReturnsActivityEntity(): void
    {
        $this->assertInstanceOf(
            expected: ActivityEntity::class,
            actual:   $this->class->toPersistence(domain: $this->makeDomain())
        );
    }

    /**
     * Test that toPersistence() maps the domain id as a Uuid value object on the entity.
     */
    public function testToPersistenceMapsIdAsUuid(): void
    {
        $domain = $this->makeDomain();
        $entity = $this->class->toPersistence(domain: $domain);

        $this->assertInstanceOf(expected: Uuid::class, actual: $entity->id);
        $this->assertSame(
            expected: $domain->id,
            actual:   (string) $entity->id
        );
    }

    /**
     * Test that toPersistence() maps the action field from the domain Activity to the entity.
     */
    public function testToPersistenceMapsAction(): void
    {
        $this->assertSame(
            expected: 'create',
            actual:   $this->class->toPersistence(domain: $this->makeDomain())->action
        );
    }

    /**
     * Test that toPersistence() maps the collection field from the domain Activity to the entity.
     */
    public function testToPersistenceMapsCollection(): void
    {
        $this->assertSame(
            expected: 'posts',
            actual:   $this->class->toPersistence(domain: $this->makeDomain())->collection
        );
    }

    /**
     * Test that toPersistence() maps the ip field from the domain Activity to the entity.
     */
    public function testToPersistenceMapsIp(): void
    {
        $this->assertSame(
            expected: '127.0.0.1',
            actual:   $this->class->toPersistence(domain: $this->makeDomain())->ip
        );
    }

    /**
     * Test that toPersistence() maps the userAgent field from the domain Activity to the entity.
     */
    public function testToPersistenceMapsUserAgent(): void
    {
        $this->assertSame(
            expected: 'PHPUnit',
            actual: $this->class->toPersistence(domain: $this->makeDomain())->userAgent
        );
    }

    /**
     * Test that toPersistence() maps nullable fields to null on the entity when not set on the domain Activity.
     */
    public function testToPersistenceMapsNullOptionalFields(): void
    {
        $domain = $this->makeDomain();
        $domain->setUserId(userId: null);
        $domain->setIp(ip: null);
        $domain->setUserAgent(userAgent: null);

        $entity = $this->class->toPersistence(domain: $domain);

        $this->assertNull(actual: $entity->userId);
        $this->assertNull(actual: $entity->ip);
        $this->assertNull(actual: $entity->userAgent);
    }
}
