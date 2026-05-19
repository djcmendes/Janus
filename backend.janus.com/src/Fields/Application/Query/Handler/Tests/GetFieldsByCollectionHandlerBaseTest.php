<?php

declare(strict_types=1);

namespace App\Fields\Application\Query\Handler\Tests;

use App\Fields\Application\Query\Handler\GetFieldsByCollectionHandler;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: GetFieldsByCollectionHandler::class)]
final class GetFieldsByCollectionHandlerBaseTest extends GetFieldsByCollectionHandlerTest
{
    public function testHandlerInstantiates(): void
    {
        $this->assertInstanceOf(GetFieldsByCollectionHandler::class, $this->handler);
    }
}
