<?php

/**
 * @file GetCollectionByNameQueryTest.php
 *
 * Tests for GetCollectionByNameQuery.
 *
 * @package App\Collections\Application\Query\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Query\Tests;

use App\Collections\Application\Query\GetCollectionByNameQuery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GetCollectionByNameQuery::class)]
final class GetCollectionByNameQueryTest extends TestCase
{
    // Happy path ───────────────────────────────────────────────────

    public function testConstructorSetsName(): void
    {
        $query = new GetCollectionByNameQuery('articles');

        $this->assertSame('articles', $query->name);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testDifferentNamesAreDistinct(): void
    {
        $a = new GetCollectionByNameQuery('articles');
        $b = new GetCollectionByNameQuery('posts');

        $this->assertNotSame($a->name, $b->name);
    }
}
