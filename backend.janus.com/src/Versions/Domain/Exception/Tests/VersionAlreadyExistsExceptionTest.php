<?php

/**
 * @file VersionAlreadyExistsExceptionTest.php
 *
 * Abstract base for all VersionAlreadyExistsException test suites.
 *
 * @package App\Versions\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Domain\Exception\Tests;

use App\Versions\Domain\Exception\VersionAlreadyExistsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for VersionAlreadyExistsException tests.
 */
#[CoversClass(VersionAlreadyExistsException::class)]
abstract class VersionAlreadyExistsExceptionTest extends TestCase
{
    /**
     * @var VersionAlreadyExistsException
     */
    protected VersionAlreadyExistsException $class;

    /**
     * @var ReflectionClass<VersionAlreadyExistsException>
     */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->class      = new VersionAlreadyExistsException('articles', 'item-uuid-1', 'main');
        $this->reflection = new ReflectionClass(VersionAlreadyExistsException::class);
    }

    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }
}
