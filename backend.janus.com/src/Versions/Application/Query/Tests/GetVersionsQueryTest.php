<?php

/**
 * @file GetVersionsQueryTest.php
 *
 * Abstract base for all GetVersionsQuery test suites.
 *
 * @package App\Versions\Application\Query\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Query\Tests;

use App\Versions\Application\Query\GetVersionsQuery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for GetVersionsQuery tests.
 *
 * Strategy: GetVersionsQuery is a final value object with no dependencies.
 * Tests instantiate it directly — no mocking required.
 */
#[CoversClass(GetVersionsQuery::class)]
abstract class GetVersionsQueryTest extends TestCase
{
    /**
     * @var GetVersionsQuery
     */
    protected GetVersionsQuery $class;

    /**
     * @var ReflectionClass<GetVersionsQuery>
     */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->class      = new GetVersionsQuery(limit: 25, offset: 0, collection: null, item: null);
        $this->reflection = new ReflectionClass(GetVersionsQuery::class);
    }

    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }
}
