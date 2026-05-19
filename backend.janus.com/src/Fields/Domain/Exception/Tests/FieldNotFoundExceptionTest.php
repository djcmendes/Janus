<?php

declare(strict_types=1);

namespace App\Fields\Domain\Exception\Tests;

use App\Fields\Domain\Exception\FieldNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: FieldNotFoundException::class)]
final class FieldNotFoundExceptionTest extends TestCase
{
    public function testMessageContainsFieldName(): void
    {
        $e = new FieldNotFoundException('articles', 'title');

        $this->assertStringContainsString('title', $e->getMessage());
    }

    public function testMessageContainsCollectionName(): void
    {
        $e = new FieldNotFoundException('articles', 'title');

        $this->assertStringContainsString('articles', $e->getMessage());
    }

    public function testIsRuntimeException(): void
    {
        $e = new FieldNotFoundException('articles', 'title');

        $this->assertInstanceOf(\RuntimeException::class, $e);
    }
}
