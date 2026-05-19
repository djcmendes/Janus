<?php

/**
 * @file GetVersionsQueryBaseTest.php
 *
 * Tests for GetVersionsQuery construction and property access.
 *
 * @package App\Versions\Application\Query\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Query\Tests;

use App\Versions\Application\Query\GetVersionsQuery;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that GetVersionsQuery stores all constructor arguments as readonly properties.
 */
#[CoversClass(className: GetVersionsQuery::class)]
final class GetVersionsQueryBaseTest extends GetVersionsQueryTest
{
    public function testLimitIsStored(): void
    {
        $this->assertSame(25, $this->class->limit);
    }

    public function testOffsetIsStored(): void
    {
        $this->assertSame(0, $this->class->offset);
    }

    public function testCollectionIsNullByDefault(): void
    {
        $this->assertNull($this->class->collection);
    }

    public function testItemIsNullByDefault(): void
    {
        $this->assertNull($this->class->item);
    }

    public function testFiltersAreStoredWhenProvided(): void
    {
        $query = new GetVersionsQuery(10, 5, 'articles', 'item-uuid-1');

        $this->assertSame(10, $query->limit);
        $this->assertSame(5, $query->offset);
        $this->assertSame('articles', $query->collection);
        $this->assertSame('item-uuid-1', $query->item);
    }
}
