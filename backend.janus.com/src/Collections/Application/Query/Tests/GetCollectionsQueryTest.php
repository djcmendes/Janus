<?php

/**
 * @file GetCollectionsQueryTest.php
 *
 * Tests for GetCollectionsQuery.
 *
 * @package App\Collections\Application\Query\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Query\Tests;

use App\Collections\Application\Query\GetCollectionsQuery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: GetCollectionsQuery::class)]
final class GetCollectionsQueryTest extends TestCase
{
    // Happy path ───────────────────────────────────────────────────

    public function testConstructorSetsLimit(): void
    {
        $query = new GetCollectionsQuery(limit: 10, offset: 0);

        $this->assertSame(10, $query->limit);
    }

    public function testConstructorSetsOffset(): void
    {
        $query = new GetCollectionsQuery(limit: 25, offset: 50);

        $this->assertSame(50, $query->offset);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testConstructorAcceptsZeroOffset(): void
    {
        $query = new GetCollectionsQuery(limit: 25, offset: 0);

        $this->assertSame(0, $query->offset);
    }
}
