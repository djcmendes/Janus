<?php

declare(strict_types=1);

namespace App\Extensions\Domain\Exception\Tests;

use App\Extensions\Domain\Exception\ExtensionNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ExtensionNotFoundException::class)]
final class ExtensionNotFoundExceptionBaseTest extends ExtensionNotFoundExceptionTest
{
    public function testExtendsRuntimeException(): void
    {
        $ex = new ExtensionNotFoundException('test-id');

        $this->assertInstanceOf(\RuntimeException::class, $ex);
    }

    public function testMessageContainsId(): void
    {
        $ex = new ExtensionNotFoundException('my-uuid');

        $this->assertStringContainsString('my-uuid', $ex->getMessage());
    }

    public function testMessageFormat(): void
    {
        $ex = new ExtensionNotFoundException('abc-123');

        $this->assertSame("Extension 'abc-123' not found.", $ex->getMessage());
    }
}
