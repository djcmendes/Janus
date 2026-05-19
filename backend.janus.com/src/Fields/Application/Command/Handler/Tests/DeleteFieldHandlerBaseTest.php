<?php

declare(strict_types=1);

namespace App\Fields\Application\Command\Handler\Tests;

use App\Fields\Application\Command\Handler\DeleteFieldHandler;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: DeleteFieldHandler::class)]
final class DeleteFieldHandlerBaseTest extends DeleteFieldHandlerTest
{
    public function testHandlerInstantiates(): void
    {
        $this->assertInstanceOf(DeleteFieldHandler::class, $this->handler);
    }
}
