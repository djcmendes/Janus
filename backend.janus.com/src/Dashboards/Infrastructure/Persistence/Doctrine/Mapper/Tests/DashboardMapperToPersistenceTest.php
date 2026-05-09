<?php

/**
 * @file DashboardMapperToPersistenceTest.php
 *
 * Tests for DashboardMapper::toPersistence().
 *
 * @package App\Dashboards\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Dashboards\Infrastructure\Persistence\Doctrine\Entity\DashboardEntity;
use App\Dashboards\Infrastructure\Persistence\Doctrine\Mapper\DashboardMapper;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies toPersistence() correctly maps a Dashboard domain entity to a DashboardEntity.
 */
#[CoversClass(DashboardMapper::class)]
final class DashboardMapperToPersistenceTest extends DashboardMapperTest
{
    /**
     * Test that toPersistence() returns a DashboardEntity instance.
     */
    public function testToPersistenceReturnsDashboardEntity(): void
    {
        $result = $this->class->toPersistence($this->makeDomain());

        $this->assertInstanceOf(DashboardEntity::class, $result);
    }

    /**
     * Test that toPersistence() maps the domain ID to the entity ID.
     */
    public function testToPersistenceMapsId(): void
    {
        $result = $this->class->toPersistence($this->makeDomain());

        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $result->getId());
    }

    /**
     * Test that toPersistence() maps the domain name to the entity name.
     */
    public function testToPersistenceMapsName(): void
    {
        $result = $this->class->toPersistence($this->makeDomain());

        $this->assertSame('Test Dashboard', $result->getName());
    }

    /**
     * Test that toPersistence() maps the domain icon to the entity icon.
     */
    public function testToPersistenceMapsIcon(): void
    {
        $result = $this->class->toPersistence($this->makeDomain());

        $this->assertSame('chart', $result->getIcon());
    }

    /**
     * Test that toPersistence() maps the domain note to the entity note.
     */
    public function testToPersistenceMapsNote(): void
    {
        $result = $this->class->toPersistence($this->makeDomain());

        $this->assertSame('A note', $result->getNote());
    }

    /**
     * Test that toPersistence() maps the domain userId to the entity userId.
     */
    public function testToPersistenceMapsUserId(): void
    {
        $result = $this->class->toPersistence($this->makeDomain());

        $this->assertSame('user-uuid-001', $result->getUserId());
    }

    /**
     * Test that toPersistence() maps the domain createdAt to the entity createdAt.
     */
    public function testToPersistenceMapsCreatedAt(): void
    {
        $result = $this->class->toPersistence($this->makeDomain());

        $this->assertSame($this->createdAt, $result->getCreatedAt());
    }

    /**
     * Test that toPersistence() maps the domain updatedAt to the entity updatedAt.
     */
    public function testToPersistenceMapsUpdatedAt(): void
    {
        $result = $this->class->toPersistence($this->makeDomain());

        $this->assertSame($this->updatedAt, $result->getUpdatedAt());
    }
}
