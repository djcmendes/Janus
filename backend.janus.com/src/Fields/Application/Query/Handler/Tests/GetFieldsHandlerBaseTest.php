<?php

declare(strict_types=1);

namespace App\Fields\Application\Query\Handler\Tests;

use App\Fields\Application\Query\Handler\GetFieldsHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionClass;

#[CoversClass(className: GetFieldsHandler::class)]
final class GetFieldsHandlerBaseTest extends GetFieldsHandlerTest
{
    public function testHandlerInstantiates(): void
    {
        $this->assertInstanceOf(GetFieldsHandler::class, $this->handler);
    }

    public function testHandlerHasHandleMethod(): void
    {
        $ref = new ReflectionClass(GetFieldsHandler::class);

        $this->assertTrue($ref->hasMethod('handle'));
    }
}
