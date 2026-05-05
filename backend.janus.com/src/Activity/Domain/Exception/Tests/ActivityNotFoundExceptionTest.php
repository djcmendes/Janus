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
    /** @var string */
    protected const string LOOKUP_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    protected ActivityNotFoundException $class;

    /** @var ReflectionClass<ActivityNotFoundException> */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->class      = new ActivityNotFoundException(self::LOOKUP_UUID);
        $this->reflection = new ReflectionClass(ActivityNotFoundException::class);
    }

    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }
}
