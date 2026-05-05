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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(ActivityMapper::class)]
#[CoversMethod(ActivityMapper::class, 'toDomain')]
final class ActivityMapperToDomainTest extends ActivityMapperTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testToDomainReturnsDomainActivity(): void
    {
        $this->assertInstanceOf(Activity::class, $this->class->toDomain($this->makeEntity()));
    }

    public function testToDomainMapsId(): void
    {
        $this->assertSame(self::FIXED_UUID, $this->class->toDomain($this->makeEntity())->getId());
    }

    public function testToDomainMapsAction(): void
    {
        $this->assertSame('create', $this->class->toDomain($this->makeEntity())->getAction());
    }

    public function testToDomainMapsCollection(): void
    {
        $this->assertSame('posts', $this->class->toDomain($this->makeEntity())->getCollection());
    }

    public function testToDomainMapsItem(): void
    {
        $this->assertSame('42', $this->class->toDomain($this->makeEntity())->getItem());
    }

    public function testToDomainMapsUserId(): void
    {
        $this->assertSame(
            'bbbbbbbb-0000-7000-8000-000000000002',
            $this->class->toDomain($this->makeEntity())->getUserId(),
        );
    }

    public function testToDomainMapsIp(): void
    {
        $this->assertSame('127.0.0.1', $this->class->toDomain($this->makeEntity())->getIp());
    }

    public function testToDomainMapsUserAgent(): void
    {
        $this->assertSame('PHPUnit', $this->class->toDomain($this->makeEntity())->getUserAgent());
    }

    public function testToDomainMapsTimestamp(): void
    {
        $ts     = new \DateTimeImmutable('2024-01-01T00:00:00+00:00');
        $entity = $this->makeEntity()->setTimestamp($ts);

        $this->assertEquals($ts, $this->class->toDomain($entity)->getTimestamp());
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testToDomainHandlesNullOptionalFields(): void
    {
        $entity = $this->makeEntity()
            ->setCollection(null)
            ->setItem(null)
            ->setUserId(null)
            ->setIp(null)
            ->setUserAgent(null);

        $domain = $this->class->toDomain($entity);

        $this->assertNull($domain->getCollection());
        $this->assertNull($domain->getItem());
        $this->assertNull($domain->getUserId());
        $this->assertNull($domain->getIp());
        $this->assertNull($domain->getUserAgent());
    }

    // Roundtrip ────────────────────────────────────────────────────

    public function testRoundtripPreservesId(): void
    {
        $domain = $this->makeDomain();
        $result = $this->class->toDomain($this->class->toPersistence($domain));

        $this->assertSame($domain->getId(), $result->getId());
    }

    public function testRoundtripPreservesAction(): void
    {
        $domain = $this->makeDomain();
        $result = $this->class->toDomain($this->class->toPersistence($domain));

        $this->assertSame($domain->getAction(), $result->getAction());
    }

    public function testRoundtripPreservesUserId(): void
    {
        $domain = $this->makeDomain();
        $result = $this->class->toDomain($this->class->toPersistence($domain));

        $this->assertSame($domain->getUserId(), $result->getUserId());
    }
}
