<?php

declare(strict_types=1);

namespace App\Fields\Application\Query\Tests;

use App\Fields\Application\Query\GetFieldByCollectionAndNameQuery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: GetFieldByCollectionAndNameQuery::class)]
final class GetFieldByCollectionAndNameQueryTest extends TestCase
{
    public function testStoresCollection(): void
    {
        $q = new GetFieldByCollectionAndNameQuery('articles', 'title');

        $this->assertSame('articles', $q->collection);
    }

    public function testStoresField(): void
    {
        $q = new GetFieldByCollectionAndNameQuery('articles', 'title');

        $this->assertSame('title', $q->field);
    }
}
