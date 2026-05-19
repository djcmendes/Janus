<?php

/**
 * @file DeploymentProviderInactiveExceptionTest.php
 *
 * Abstract base for DeploymentProviderInactiveException test cases.
 *
 * @package App\Deployments\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Exception\Tests;

use App\Deployments\Domain\Exception\DeploymentProviderInactiveException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and factory helpers for DeploymentProviderInactiveException test suites.
 */
#[CoversClass(className: DeploymentProviderInactiveException::class)]
abstract class DeploymentProviderInactiveExceptionTest extends TestCase
{
    /** @var DeploymentProviderInactiveException The system under test. */
    protected DeploymentProviderInactiveException $class;

    /**
     * Builds the SUT before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->class = new DeploymentProviderInactiveException('aaaaaaaa-0000-7000-8000-000000000001');
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
