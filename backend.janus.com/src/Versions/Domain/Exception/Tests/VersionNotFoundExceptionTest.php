<?php

/**
 * @file VersionNotFoundExceptionTest.php
 *
 * Abstract base for all VersionNotFoundException test suites.
 *
 * @package App\Versions\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Domain\Exception\Tests;

use App\Versions\Domain\Exception\VersionNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for VersionNotFoundException tests.
 *
 * Strategy: VersionNotFoundException is a final class with no injectable dependencies.
 * Tests instantiate it directly — no mocking is required.
 */
#[CoversClass(className: VersionNotFoundException::class)]
abstract class VersionNotFoundExceptionTest extends TestCase
{
    /** @var string */
    protected const string MISSING_UUID = 'cccccccc-0000-7000-8000-000000000003';

    /**
     * @var VersionNotFoundException
     */
    protected VersionNotFoundException $class;

    /**
     * @var ReflectionClass<VersionNotFoundException>
     */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->class      = new VersionNotFoundException(self::MISSING_UUID);
        $this->reflection = new ReflectionClass(VersionNotFoundException::class);
    }

    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }
}
