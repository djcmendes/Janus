<?php

/**
 * @file DashboardMapperBaseTest.php
 *
 * Constructor and interface compliance tests for DashboardMapper.
 *
 * @package App\Dashboards\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Dashboards\Infrastructure\Persistence\Doctrine\Mapper\DashboardMapper;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies DashboardMapper instantiation.
 */
#[CoversClass(DashboardMapper::class)]
final class DashboardMapperBaseTest extends DashboardMapperTest
{
    /**
     * Test that the SUT is an instance of DashboardMapper.
     */
    public function testIsInstanceOfDashboardMapper(): void
    {
        $this->assertInstanceOf(DashboardMapper::class, $this->class);
    }
}
