<?php

/**
 * @file DashboardNotFoundExceptionTest.php
 *
 * Abstract base for DashboardNotFoundException test suites.
 *
 * @package App\Dashboards\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Domain\Exception\Tests;

use App\Dashboards\Domain\Exception\DashboardNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Common setup and teardown for DashboardNotFoundException test cases.
 */
#[CoversClass(className: DashboardNotFoundException::class)]
abstract class DashboardNotFoundExceptionTest extends TestCase
{
    /** @var DashboardNotFoundException The system under test. */
    protected DashboardNotFoundException $class;

    /**
     * Builds the SUT before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->class = new DashboardNotFoundException('test-uuid-001');
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
}
