<?php

/**
 * @file DashboardEntityBaseTest.php
 *
 * Getter/setter compliance tests for DashboardEntity.
 *
 * @package App\Dashboards\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Dashboards\Infrastructure\Persistence\Doctrine\Entity\DashboardEntity;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that all getters return the values set by the corresponding fluent setters.
 */
#[CoversClass(className: DashboardEntity::class)]
final class DashboardEntityBaseTest extends DashboardEntityTest
{
    /**
     * Test that the SUT is an instance of DashboardEntity.
     */
    public function testIsInstanceOfDashboardEntity(): void
    {
        $this->assertInstanceOf(DashboardEntity::class, $this->class);
    }

    /**
     * Test that getId() returns the value set by setId().
     */
    public function testGetIdReturnsSetValue(): void
    {
        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $this->class->getId());
    }

    /**
     * Test that getName() returns the value set by setName().
     */
    public function testGetNameReturnsSetValue(): void
    {
        $this->assertSame('Test Dashboard', $this->class->getName());
    }

    /**
     * Test that getIcon() returns the value set by setIcon().
     */
    public function testGetIconReturnsSetValue(): void
    {
        $this->assertSame('chart', $this->class->getIcon());
    }

    /**
     * Test that getNote() returns the value set by setNote().
     */
    public function testGetNoteReturnsSetValue(): void
    {
        $this->assertSame('Some note', $this->class->getNote());
    }

    /**
     * Test that getUserId() returns the value set by setUserId().
     */
    public function testGetUserIdReturnsSetValue(): void
    {
        $this->assertSame('user-uuid-001', $this->class->getUserId());
    }

    /**
     * Test that getCreatedAt() returns the value set by setCreatedAt().
     */
    public function testGetCreatedAtReturnsSetValue(): void
    {
        $this->assertEquals(new \DateTimeImmutable('2024-01-01T00:00:00Z'), $this->class->getCreatedAt());
    }

    /**
     * Test that getUpdatedAt() returns the value set by setUpdatedAt().
     */
    public function testGetUpdatedAtReturnsSetValue(): void
    {
        $this->assertEquals(new \DateTimeImmutable('2024-06-01T00:00:00Z'), $this->class->getUpdatedAt());
    }

    /**
     * Test that all setters return the same entity instance (fluent interface).
     */
    public function testAllSettersReturnSelf(): void
    {
        $entity = new DashboardEntity();

        $this->assertSame($entity, $entity->setId('id'));
        $this->assertSame($entity, $entity->setName('name'));
        $this->assertSame($entity, $entity->setIcon(null));
        $this->assertSame($entity, $entity->setNote(null));
        $this->assertSame($entity, $entity->setUserId(null));
        $this->assertSame($entity, $entity->setCreatedAt(new \DateTimeImmutable()));
        $this->assertSame($entity, $entity->setUpdatedAt(new \DateTimeImmutable()));
    }

    /**
     * Test that setIcon(null) and setNote(null) are accepted (nullable fields).
     */
    public function testNullableFieldsAcceptNull(): void
    {
        $this->class->setIcon(null)->setNote(null)->setUserId(null);

        $this->assertNull($this->class->getIcon());
        $this->assertNull($this->class->getNote());
        $this->assertNull($this->class->getUserId());
    }
}
