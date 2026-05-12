<?php

/**
 * @file GetVersionByIdQueryTest.php
 *
 * Abstract base for all GetVersionByIdQuery test suites.
 *
 * @package App\Versions\Application\Query\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Query\Tests;

use App\Versions\Application\Query\GetVersionByIdQuery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for GetVersionByIdQuery tests.
 */
#[CoversClass(GetVersionByIdQuery::class)]
abstract class GetVersionByIdQueryTest extends TestCase
{
    /** @var string */
    protected const string LOOKUP_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * @var GetVersionByIdQuery
     */
    protected GetVersionByIdQuery $class;

    /**
     * @var ReflectionClass<GetVersionByIdQuery>
     */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->class      = new GetVersionByIdQuery(id: self::LOOKUP_UUID);
        $this->reflection = new ReflectionClass(GetVersionByIdQuery::class);
    }

    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }
}
