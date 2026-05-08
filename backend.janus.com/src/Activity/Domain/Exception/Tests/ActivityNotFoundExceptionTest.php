<?php

/**
 * @file ActivityNotFoundExceptionTest.php
 *
 * Abstract base for all ActivityNotFoundException test suites.
 *
 * @package App\Activity\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Domain\Exception\Tests;

use App\Activity\Domain\Exception\ActivityNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for ActivityNotFoundException tests.
 *
 * Strategy: ActivityNotFoundException is a final class with no injectable
 * dependencies. Tests instantiate it directly using a deterministic UUID
 * constant — no mocks are required.
 */
#[CoversClass(ActivityNotFoundException::class)]
abstract class ActivityNotFoundExceptionTest extends TestCase
{
    /**
     * UUID used as the lookup identifier in all get() test scenarios.
     * @var string
     */
    protected const string LOOKUP_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * Instance of the class being tested
     * @var ActivityNotFoundException
     */
    protected ActivityNotFoundException $class;

    /**
     * Reflection of ActivityNotFoundException class
     * @var ReflectionClass<ActivityNotFoundException>
     */
    protected ReflectionClass $reflection;

    /**
     * TestCase Constructor.
     * Builds the SUT and its reflection mirror before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->class      = new ActivityNotFoundException(id: self::LOOKUP_UUID);
        $this->reflection = new ReflectionClass(objectOrClass: ActivityNotFoundException::class);
    }

    /**
     * TestCase Destructor.
     * Releases SUT references after each test to prevent state bleed.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset(
            $this->class,
            $this->reflection
        );
    }
}
