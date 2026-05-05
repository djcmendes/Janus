<?php

/**
 * @file GetActivityByIdQueryTest.php
 *
 * Abstract base for all GetActivityByIdQuery test suites.
 *
 * @package App\Activity\Application\Query\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Query\Tests;

use App\Activity\Application\Query\GetActivityByIdQuery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for GetActivityByIdQuery tests.
 *
 * Strategy: GetActivityByIdQuery is a final class with no injectable dependencies.
 * Tests instantiate it directly with a deterministic UUID — no mocks required.
 */
#[CoversClass(GetActivityByIdQuery::class)]
abstract class GetActivityByIdQueryTest extends TestCase
{
    /** @var string */
    protected const string LOOKUP_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    protected GetActivityByIdQuery $class;

    /** @var ReflectionClass<GetActivityByIdQuery> */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->class      = new GetActivityByIdQuery(self::LOOKUP_UUID);
        $this->reflection = new ReflectionClass(GetActivityByIdQuery::class);
    }

    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }
}
