<?php

declare(strict_types=1);

namespace App\Fields\Application\Command\Handler\Tests;

use App\Fields\Application\Command\Handler\CreateFieldHandler;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: CreateFieldHandler::class)]
final class CreateFieldHandlerBaseTest extends CreateFieldHandlerTest
{
    public function testHandlerInstantiates(): void
    {
        $this->assertInstanceOf(CreateFieldHandler::class, $this->handler);
    }
}
