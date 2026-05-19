<?php

/**
 * @file DashboardDtoBaseTest.php
 *
 * Constructor compliance tests for DashboardDto.
 *
 * @package App\Dashboards\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\DTO\Tests;

use App\Dashboards\Application\DTO\DashboardDto;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that DashboardDto correctly stores all constructor arguments.
 */
#[CoversClass(className: DashboardDto::class)]
final class DashboardDtoBaseTest extends DashboardDtoTest
{
    /**
     * Test that the SUT is an instance of DashboardDto.
     */
    public function testIsInstanceOfDashboardDto(): void
    {
        $this->assertInstanceOf(DashboardDto::class, $this->class);
    }

    /**
     * Test that the id property holds the constructor value.
     */
    public function testIdIsStored(): void
    {
        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $this->class->id);
    }

    /**
     * Test that the name property holds the constructor value.
     */
    public function testNameIsStored(): void
    {
        $this->assertSame('Test Dashboard', $this->class->name);
    }

    /**
     * Test that the icon property holds the constructor value.
     */
    public function testIconIsStored(): void
    {
        $this->assertSame('chart', $this->class->icon);
    }

    /**
     * Test that the note property holds the constructor value.
     */
    public function testNoteIsStored(): void
    {
        $this->assertSame('A note', $this->class->note);
    }

    /**
     * Test that the userId property holds the constructor value.
     */
    public function testUserIdIsStored(): void
    {
        $this->assertSame('user-uuid-001', $this->class->userId);
    }

    /**
     * Test that nullable icon and userId accept null.
     */
    public function testNullableFieldsAcceptNull(): void
    {
        $dto = new DashboardDto('id', 'name', null, null, null, '2024-01-01', '2024-01-01');

        $this->assertNull($dto->icon);
        $this->assertNull($dto->note);
        $this->assertNull($dto->userId);
    }
}
