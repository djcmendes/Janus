<?php

/**
 * @file ActivityLoggerTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * shared mock properties for all ActivityLogger test cases.
 *
 * Strategy: ActivityLogger is declared `final` and cannot be mocked directly.
 * It is instantiated as a real object. Its dependencies — ActivityRepositoryInterface
 * (an interface) and RequestStack (a non-final Symfony class) — are mocked normally.
 *
 * @package App\Activity\Domain\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Domain\Service\Tests;

use App\Activity\Domain\Repository\ActivityRepositoryInterface;
use App\Activity\Domain\Service\ActivityLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Common setup, teardown, and shared mock infrastructure for all
 * ActivityLogger test suites.
 */
#[CoversClass(ActivityLogger::class)]
abstract class ActivityLoggerTest extends TestCase
{
    /**
     * Mock of the domain repository interface.
     * @var MockObject&ActivityRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * Mock of the Symfony request stack.
     * @var MockObject&RequestStack
     */
    protected MockObject $requestStack;

    /**
     * The system under test — real ActivityLogger backed by mocked dependencies.
     * @var ActivityLogger
     */
    protected ActivityLogger $class;

    /**
     * Reflection of ActivityLogger for reading private properties.
     * @var ReflectionClass<ActivityLogger>
     */
    protected ReflectionClass $reflection;

    public function setUp(): void
    {
        $this->repository   = $this->createMock(ActivityRepositoryInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->class        = new ActivityLogger($this->repository, $this->requestStack);
        $this->reflection   = new ReflectionClass(ActivityLogger::class);
    }

    public function tearDown(): void
    {
        unset($this->repository, $this->requestStack, $this->class, $this->reflection);
    }
}
