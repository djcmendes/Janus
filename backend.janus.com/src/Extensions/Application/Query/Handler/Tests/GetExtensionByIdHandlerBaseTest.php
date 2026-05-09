<?php

declare(strict_types=1);

namespace App\Extensions\Application\Query\Handler\Tests;

use App\Extensions\Application\Query\Handler\GetExtensionByIdHandler;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GetExtensionByIdHandler::class)]
final class GetExtensionByIdHandlerBaseTest extends GetExtensionByIdHandlerTest
{
    public function testHandlerCanBeInstantiated(): void
    {
        $this->assertInstanceOf(GetExtensionByIdHandler::class, $this->handler);
    }
}
