<?php

/**
 * @file GetActivityQueryTest.php
 *
 * Abstract base for all GetActivityQuery test suites.
 *
 * @package App\Activity\Application\Query\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Query\Tests;

use App\Activity\Application\Query\GetActivityQuery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for GetActivityQuery tests.
 *
 * Strategy: GetActivityQuery is a final class with no injectable dependencies.
 * Tests instantiate it directly with deterministic values — no mocks required.
 */
#[CoversClass(className:  GetActivityQuery::class)]
abstract class GetActivityQueryTest extends TestCase
{
    /**
     * The instance of the query being tested.
     * @var GetActivityQuery
     */
    protected GetActivityQuery $class;

    /**
     * Reflection of GetActivityQuery class
     * @var ReflectionClass<GetActivityQuery>
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
        $this->class      = new GetActivityQuery(limit: 25, offset: 0);
        $this->reflection = new ReflectionClass(objectOrClass: GetActivityQuery::class);
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
