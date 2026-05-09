<?php

/**
 * @file DeploymentNotFoundExceptionTest.php
 *
 * Abstract base for DeploymentNotFoundException test cases.
 *
 * @package App\Deployments\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Exception\Tests;

use App\Deployments\Domain\Exception\DeploymentNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and factory helpers for DeploymentNotFoundException test suites.
 */
#[CoversClass(DeploymentNotFoundException::class)]
abstract class DeploymentNotFoundExceptionTest extends TestCase
{
    /** @var DeploymentNotFoundException The system under test. */
    protected DeploymentNotFoundException $class;

    /**
     * Builds the SUT before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->class = new DeploymentNotFoundException('aaaaaaaa-0000-7000-8000-000000000001');
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
