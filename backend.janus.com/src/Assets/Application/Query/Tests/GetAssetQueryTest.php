<?php

/**
 * @file GetAssetQueryTest.php
 *
 * Abstract base for all GetAssetQuery test suites.
 *
 * Strategy: GetAssetQuery is a final class with no injectable dependencies.
 * Tests instantiate it directly with deterministic values — no mocks required.
 *
 * @package App\Assets\Application\Query\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Assets\Application\Query\Tests;

use App\Assets\Application\Query\GetAssetQuery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for GetAssetQuery tests.
 */
#[CoversClass(className: GetAssetQuery::class)]
abstract class GetAssetQueryTest extends TestCase
{
    /**
     * @var GetAssetQuery The query instance under test.
     */
    protected GetAssetQuery $class;

    /**
     * @var ReflectionClass<GetAssetQuery>
     */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->class      = new GetAssetQuery('aaaaaaaa-0000-7000-8000-000000000001', null, null, 'contain', 'jpg');
        $this->reflection = new ReflectionClass(GetAssetQuery::class);
    }

    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }
}
