<?php

/**
 * @file DashboardMapperToDomainTest.php
 *
 * Tests for DashboardMapper::toDomain().
 *
 * @package App\Dashboards\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Dashboards\Domain\Entity\Dashboard;
use App\Dashboards\Infrastructure\Persistence\Doctrine\Mapper\DashboardMapper;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies toDomain() correctly maps DashboardEntity fields to a Dashboard domain entity.
 */
#[CoversClass(className: DashboardMapper::class)]
final class DashboardMapperToDomainTest extends DashboardMapperTest
{
    /**
     * Test that toDomain() returns a Dashboard instance.
     */
    public function testToDomainReturnsDashboard(): void
    {
        $result = $this->class->toDomain($this->makeEntity());

        $this->assertInstanceOf(Dashboard::class, $result);
    }

    /**
     * Test that toDomain() maps the entity ID to the domain ID.
     */
    public function testToDomainMapsId(): void
    {
        $result = $this->class->toDomain($this->makeEntity());

        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $result->getId());
    }

    /**
     * Test that toDomain() maps the entity name to the domain name.
     */
    public function testToDomainMapsName(): void
    {
        $result = $this->class->toDomain($this->makeEntity());

        $this->assertSame('Test Dashboard', $result->getName());
    }

    /**
     * Test that toDomain() maps the entity icon to the domain icon.
     */
    public function testToDomainMapsIcon(): void
    {
        $result = $this->class->toDomain($this->makeEntity());

        $this->assertSame('chart', $result->getIcon());
    }

    /**
     * Test that toDomain() maps the entity note to the domain note.
     */
    public function testToDomainMapsNote(): void
    {
        $result = $this->class->toDomain($this->makeEntity());

        $this->assertSame('A note', $result->getNote());
    }

    /**
     * Test that toDomain() maps the entity userId to the domain userId.
     */
    public function testToDomainMapsUserId(): void
    {
        $result = $this->class->toDomain($this->makeEntity());

        $this->assertSame('user-uuid-001', $result->getUserId());
    }

    /**
     * Test that toDomain() maps the entity createdAt to the domain createdAt.
     */
    public function testToDomainMapsCreatedAt(): void
    {
        $result = $this->class->toDomain($this->makeEntity());

        $this->assertSame($this->createdAt, $result->getCreatedAt());
    }

    /**
     * Test that toDomain() maps the entity updatedAt to the domain updatedAt.
     */
    public function testToDomainMapsUpdatedAt(): void
    {
        $result = $this->class->toDomain($this->makeEntity());

        $this->assertSame($this->updatedAt, $result->getUpdatedAt());
    }

    /**
     * Test that toDomain() handles a null icon correctly.
     */
    public function testToDomainHandlesNullIcon(): void
    {
        $entity = $this->makeEntity()->setIcon(null);
        $result = $this->class->toDomain($entity);

        $this->assertNull($result->getIcon());
    }

    /**
     * Test that toDomain() handles a null userId (shared dashboard) correctly.
     */
    public function testToDomainHandlesNullUserId(): void
    {
        $entity = $this->makeEntity()->setUserId(null);
        $result = $this->class->toDomain($entity);

        $this->assertNull($result->getUserId());
    }
}
