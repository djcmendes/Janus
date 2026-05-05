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
#[CoversClass(GetActivityQuery::class)]
abstract class GetActivityQueryTest extends TestCase
{
    protected GetActivityQuery $class;

    /** @var ReflectionClass<GetActivityQuery> */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->class      = new GetActivityQuery(25, 0);
        $this->reflection = new ReflectionClass(GetActivityQuery::class);
    }

    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }
}
