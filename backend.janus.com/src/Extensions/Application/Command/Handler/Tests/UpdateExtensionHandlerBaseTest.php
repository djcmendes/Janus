<?php

declare(strict_types=1);

namespace App\Extensions\Application\Command\Handler\Tests;

use App\Extensions\Application\Command\Handler\UpdateExtensionHandler;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UpdateExtensionHandler::class)]
final class UpdateExtensionHandlerBaseTest extends UpdateExtensionHandlerTest
{
    public function testHandlerCanBeInstantiated(): void
    {
        $this->assertInstanceOf(UpdateExtensionHandler::class, $this->handler);
    }
}
