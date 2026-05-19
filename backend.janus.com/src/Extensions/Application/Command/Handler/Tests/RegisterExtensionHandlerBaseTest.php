<?php

declare(strict_types=1);

namespace App\Extensions\Application\Command\Handler\Tests;

use App\Extensions\Application\Command\Handler\RegisterExtensionHandler;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: RegisterExtensionHandler::class)]
final class RegisterExtensionHandlerBaseTest extends RegisterExtensionHandlerTest
{
    public function testHandlerCanBeInstantiated(): void
    {
        $this->assertInstanceOf(RegisterExtensionHandler::class, $this->handler);
    }
}
