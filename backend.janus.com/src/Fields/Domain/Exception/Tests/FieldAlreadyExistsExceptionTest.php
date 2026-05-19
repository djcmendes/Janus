<?php

declare(strict_types=1);

namespace App\Fields\Domain\Exception\Tests;

use App\Fields\Domain\Exception\FieldAlreadyExistsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: FieldAlreadyExistsException::class)]
final class FieldAlreadyExistsExceptionTest extends TestCase
{
    public function testMessageContainsFieldName(): void
    {
        $e = new FieldAlreadyExistsException('articles', 'slug');

        $this->assertStringContainsString('slug', $e->getMessage());
    }

    public function testMessageContainsCollectionName(): void
    {
        $e = new FieldAlreadyExistsException('articles', 'slug');

        $this->assertStringContainsString('articles', $e->getMessage());
    }

    public function testIsRuntimeException(): void
    {
        $e = new FieldAlreadyExistsException('articles', 'slug');

        $this->assertInstanceOf(\RuntimeException::class, $e);
    }
}
