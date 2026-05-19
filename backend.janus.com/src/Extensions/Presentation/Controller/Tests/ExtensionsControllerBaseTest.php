<?php

declare(strict_types=1);

namespace App\Extensions\Presentation\Controller\Tests;

use App\Extensions\Presentation\Controller\ExtensionsController;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: ExtensionsController::class)]
final class ExtensionsControllerBaseTest extends ExtensionsControllerTest
{
    public function testControllerCanBeInstantiated(): void
    {
        $this->assertInstanceOf(ExtensionsController::class, $this->class);
    }
}
