<?php

declare(strict_types=1);

namespace App\Extensions\Application\Query\Handler\Tests;

use App\Extensions\Application\Query\Handler\GetExtensionsHandler;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GetExtensionsHandler::class)]
final class GetExtensionsHandlerBaseTest extends GetExtensionsHandlerTest
{
    public function testHandlerCanBeInstantiated(): void
    {
        $this->assertInstanceOf(GetExtensionsHandler::class, $this->handler);
    }
}
