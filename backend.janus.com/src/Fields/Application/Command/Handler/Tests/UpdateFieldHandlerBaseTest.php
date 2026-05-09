<?php

declare(strict_types=1);

namespace App\Fields\Application\Command\Handler\Tests;

use App\Fields\Application\Command\Handler\UpdateFieldHandler;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UpdateFieldHandler::class)]
final class UpdateFieldHandlerBaseTest extends UpdateFieldHandlerTest
{
    public function testHandlerInstantiates(): void
    {
        $this->assertInstanceOf(UpdateFieldHandler::class, $this->handler);
    }
}
