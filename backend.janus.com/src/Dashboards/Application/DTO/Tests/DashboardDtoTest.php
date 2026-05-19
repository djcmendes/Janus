<?php

/**
 * @file DashboardDtoTest.php
 *
 * Abstract base for DashboardDto test suites.
 *
 * @package App\Dashboards\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\DTO\Tests;

use App\Dashboards\Application\DTO\DashboardDto;
use App\Dashboards\Domain\Entity\Dashboard;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and factory helpers for DashboardDto test suites.
 */
#[CoversClass(className: DashboardDto::class)]
abstract class DashboardDtoTest extends TestCase
{
    /** @var DashboardDto The system under test. */
    protected DashboardDto $class;

    /**
     * Builds the SUT before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->class = new DashboardDto(
            id:        'aaaaaaaa-0000-7000-8000-000000000001',
            name:      'Test Dashboard',
            icon:      'chart',
            note:      'A note',
            userId:    'user-uuid-001',
            createdAt: '2024-01-01T00:00:00+00:00',
            updatedAt: '2024-06-01T00:00:00+00:00',
        );
    }

    /**
     * Releases the SUT reference after each test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->class);
    }

    /**
     * Creates a domain Dashboard via reconstitute() for fromEntity() tests.
     *
     * @return Dashboard
     */
    protected function makeDashboard(): Dashboard
    {
        return Dashboard::reconstitute(
            id:        'aaaaaaaa-0000-7000-8000-000000000001',
            name:      'Test Dashboard',
            icon:      'chart',
            note:      'A note',
            userId:    'user-uuid-001',
            createdAt: new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new \DateTimeImmutable('2024-06-01T00:00:00Z'),
        );
    }
}
