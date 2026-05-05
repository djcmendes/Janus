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

#[CoversClass(ActivityMapper::class)]
#[CoversMethod(ActivityMapper::class, 'toPersistence')]
final class ActivityMapperToPersistenceTest extends ActivityMapperTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testToPersistenceReturnsActivityEntity(): void
    {
        $this->assertInstanceOf(ActivityEntity::class, $this->class->toPersistence($this->makeDomain()));
    }

    public function testToPersistenceMapsIdAsUuid(): void
    {
        $domain = $this->makeDomain();
        $entity = $this->class->toPersistence($domain);

        $this->assertInstanceOf(Uuid::class, $entity->getId());
        $this->assertSame($domain->getId(), (string) $entity->getId());
    }

    public function testToPersistenceMapsAction(): void
    {
        $this->assertSame('create', $this->class->toPersistence($this->makeDomain())->getAction());
    }

    public function testToPersistenceMapsCollection(): void
    {
        $this->assertSame('posts', $this->class->toPersistence($this->makeDomain())->getCollection());
    }

    public function testToPersistenceMapsIp(): void
    {
        $this->assertSame('127.0.0.1', $this->class->toPersistence($this->makeDomain())->getIp());
    }

    public function testToPersistenceMapsUserAgent(): void
    {
        $this->assertSame('PHPUnit', $this->class->toPersistence($this->makeDomain())->getUserAgent());
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testToPersistenceMapsNullOptionalFields(): void
    {
        $domain = $this->makeDomain();
        $domain->setUserId(null);
        $domain->setIp(null);
        $domain->setUserAgent(null);

        $entity = $this->class->toPersistence($domain);

        $this->assertNull($entity->getUserId());
        $this->assertNull($entity->getIp());
        $this->assertNull($entity->getUserAgent());
    }
}
