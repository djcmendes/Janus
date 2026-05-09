<?php

declare(strict_types=1);

namespace App\Fields\Application\Query\Handler\Tests;

use App\Fields\Application\Query\Handler\GetFieldByCollectionAndNameHandler;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GetFieldByCollectionAndNameHandler::class)]
final class GetFieldByCollectionAndNameHandlerBaseTest extends GetFieldByCollectionAndNameHandlerTest
{
    public function testHandlerInstantiates(): void
    {
        $this->assertInstanceOf(GetFieldByCollectionAndNameHandler::class, $this->handler);
    }
}
