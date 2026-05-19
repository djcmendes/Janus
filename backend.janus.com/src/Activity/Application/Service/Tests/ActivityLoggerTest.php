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
 * @package App\Activity\Application\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Service\Tests;

use App\Activity\Application\Service\ActivityLogger;
use App\Activity\Domain\Repository\ActivityRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Common setup, teardown, and shared mock infrastructure for all
 * ActivityLogger test suites.
 */
#[CoversClass(className: ActivityLogger::class)]
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
     * Reflection of ActivityLogger class
     * @var ReflectionClass<ActivityLogger>
     */
    protected ReflectionClass $reflection;

    /**
     * TestCase Constructor.
     * Builds the SUT and its reflection mirror before each test.
     *
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->repository   = $this->createMock(type: ActivityRepositoryInterface::class);
        $this->requestStack = $this->createMock(type: RequestStack::class);
        $this->class        = new ActivityLogger(repository: $this->repository, requestStack: $this->requestStack);
        $this->reflection   = new ReflectionClass(objectOrClass: ActivityLogger::class);
    }

    /**
     * TestCase Destructor.
     * Releases SUT references after each test to prevent state bleed.
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset(
            $this->repository,
            $this->requestStack,
            $this->class,
            $this->reflection
        );
    }
}
