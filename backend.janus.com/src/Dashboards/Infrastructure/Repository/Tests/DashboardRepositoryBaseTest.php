<?php

/**
 * @file DashboardRepositoryBaseTest.php
 *
 * Constructor and interface compliance tests for DashboardRepository.
 *
 * @package App\Dashboards\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Infrastructure\Repository\Tests;

use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;
use App\Dashboards\Infrastructure\Repository\DashboardRepository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies DashboardRepository implements the domain contract and is correctly constructed.
 */
#[CoversClass(className: DashboardRepository::class)]
final class DashboardRepositoryBaseTest extends DashboardRepositoryTest
{
    /**
     * Test that the SUT is an instance of DashboardRepository.
     */
    public function testIsInstanceOfDashboardRepository(): void
    {
        $this->assertInstanceOf(DashboardRepository::class, $this->class);
    }

    /**
     * Test that the SUT implements DashboardRepositoryInterface.
     */
    public function testImplementsDashboardRepositoryInterface(): void
    {
        $this->assertInstanceOf(DashboardRepositoryInterface::class, $this->class);
    }
}
