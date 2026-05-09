<?php

declare(strict_types=1);

namespace App\Fields\Application\Query\Tests;

use App\Fields\Application\Query\GetFieldsQuery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GetFieldsQuery::class)]
final class GetFieldsQueryTest extends TestCase
{
    public function testStoresLimit(): void
    {
        $q = new GetFieldsQuery(50, 0);

        $this->assertSame(50, $q->limit);
    }

    public function testStoresOffset(): void
    {
        $q = new GetFieldsQuery(25, 75);

        $this->assertSame(75, $q->offset);
    }
}
