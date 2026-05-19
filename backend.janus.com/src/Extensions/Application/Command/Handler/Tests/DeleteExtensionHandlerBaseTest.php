<?php

declare(strict_types=1);

namespace App\Extensions\Application\Command\Handler\Tests;

use App\Extensions\Application\Command\Handler\DeleteExtensionHandler;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: DeleteExtensionHandler::class)]
final class DeleteExtensionHandlerBaseTest extends DeleteExtensionHandlerTest
{
    public function testHandlerCanBeInstantiated(): void
    {
        $this->assertInstanceOf(DeleteExtensionHandler::class, $this->handler);
    }
}
