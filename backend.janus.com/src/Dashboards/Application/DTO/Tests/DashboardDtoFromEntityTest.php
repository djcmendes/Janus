<?php

/**
 * @file DashboardDtoFromEntityTest.php
 *
 * Tests for DashboardDto::fromEntity().
 *
 * @package App\Dashboards\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\DTO\Tests;

use App\Dashboards\Application\DTO\DashboardDto;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies fromEntity() correctly converts a domain Dashboard to a DashboardDto.
 */
#[CoversClass(DashboardDto::class)]
final class DashboardDtoFromEntityTest extends DashboardDtoTest
{
    /**
     * Test that fromEntity() returns a DashboardDto instance.
     */
    public function testFromEntityReturnsDashboardDto(): void
    {
        $result = DashboardDto::fromEntity($this->makeDashboard());

        $this->assertInstanceOf(DashboardDto::class, $result);
    }

    /**
     * Test that fromEntity() maps the domain ID.
     */
    public function testFromEntityMapsId(): void
    {
        $result = DashboardDto::fromEntity($this->makeDashboard());

        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $result->id);
    }

    /**
     * Test that fromEntity() maps the domain name.
     */
    public function testFromEntityMapsName(): void
    {
        $result = DashboardDto::fromEntity($this->makeDashboard());

        $this->assertSame('Test Dashboard', $result->name);
    }

    /**
     * Test that fromEntity() maps the domain icon.
     */
    public function testFromEntityMapsIcon(): void
    {
        $result = DashboardDto::fromEntity($this->makeDashboard());

        $this->assertSame('chart', $result->icon);
    }

    /**
     * Test that fromEntity() maps the domain note.
     */
    public function testFromEntityMapsNote(): void
    {
        $result = DashboardDto::fromEntity($this->makeDashboard());

        $this->assertSame('A note', $result->note);
    }

    /**
     * Test that fromEntity() formats createdAt as an ATOM string.
     */
    public function testFromEntityFormatsCreatedAtAsAtom(): void
    {
        $result = DashboardDto::fromEntity($this->makeDashboard());

        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/', $result->createdAt);
    }

    /**
     * Test that fromEntity() formats updatedAt as an ATOM string.
     */
    public function testFromEntityFormatsUpdatedAtAsAtom(): void
    {
        $result = DashboardDto::fromEntity($this->makeDashboard());

        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/', $result->updatedAt);
    }
}
