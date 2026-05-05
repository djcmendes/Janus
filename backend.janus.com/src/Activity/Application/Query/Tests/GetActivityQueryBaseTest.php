<?php

/**
 * @file GetActivityQueryBaseTest.php
 *
 * Constructor and property compliance tests for GetActivityQuery.
 *
 * @package App\Activity\Application\Query\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Query\Tests;

use App\Activity\Application\Query\GetActivityQuery;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GetActivityQuery::class)]
final class GetActivityQueryBaseTest extends GetActivityQueryTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testIsInstanceOfGetActivityQuery(): void
    {
        $this->assertInstanceOf(GetActivityQuery::class, $this->class);
    }

    public function testConstructorStoresLimit(): void
    {
        $this->assertSame(25, $this->class->limit);
    }

    public function testConstructorStoresOffset(): void
    {
        $this->assertSame(0, $this->class->offset);
    }

    public function testFiltersDefaultToNull(): void
    {
        $this->assertNull($this->class->collection);
        $this->assertNull($this->class->action);
        $this->assertNull($this->class->userId);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testConstructorStoresAllFiltersWhenProvided(): void
    {
        $query = new GetActivityQuery(10, 5, 'posts', 'create', 'user-uuid');

        $this->assertSame(10, $query->limit);
        $this->assertSame(5, $query->offset);
        $this->assertSame('posts', $query->collection);
        $this->assertSame('create', $query->action);
        $this->assertSame('user-uuid', $query->userId);
    }

    public function testAllPropertiesAreReadonly(): void
    {
        foreach (['limit', 'offset', 'collection', 'action', 'userId'] as $property) {
            $this->assertTrue(
                $this->reflection->getProperty($property)->isReadOnly(),
                "Property \${$property} must be readonly.",
            );
        }
    }
}
