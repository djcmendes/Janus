<?php

declare(strict_types=1);

namespace App\Fields\Application\Query\Tests;

use App\Fields\Application\Query\GetFieldsByCollectionQuery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: GetFieldsByCollectionQuery::class)]
final class GetFieldsByCollectionQueryTest extends TestCase
{
    public function testStoresCollection(): void
    {
        $q = new GetFieldsByCollectionQuery('articles');

        $this->assertSame('articles', $q->collection);
    }
}
