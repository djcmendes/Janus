<?php

/**
 * @file ActivityTest.php
 *
 * Abstract base for all Activity domain entity test suites.
 *
 * @package App\Activity\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Domain\Entity\Tests;

use App\Activity\Domain\Entity\Activity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for Activity domain entity tests.
 *
 * Strategy: Activity is a final class with no injectable dependencies.
 * Tests instantiate it directly — no mocking is required.
 */
#[CoversClass(Activity::class)]
abstract class ActivityTest extends TestCase
{
    /**
     * Instance of the class being tested
     * @var Activity
    */
    protected Activity $class;

    /**
     * Reflection of Activity class
     * @var ReflectionClass<Activity>
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
        $this->class      = new Activity('create', 'posts', '42');
        $this->reflection = new ReflectionClass(Activity::class);
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

    /**
     * Creates a fully-populated Activity entity with deterministic test values.
     *
     * @return Activity A hydrated entity with userId, ip, and userAgent set.
     */
    protected function makeActivity(): Activity
    {
        $activity = new Activity('create', 'posts', '42');
        $activity->setUserId('bbbbbbbb-0000-7000-8000-000000000002');
        $activity->setIp('127.0.0.1');
        $activity->setUserAgent('PHPUnit');

        return $activity;
    }
}
