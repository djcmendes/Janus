<?php

/**
 * @file CollectionNotFoundExceptionTest.php
 *
 * Tests for CollectionNotFoundException.
 *
 * @package App\Collections\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Domain\Exception\Tests;

use App\Collections\Domain\Exception\CollectionNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: CollectionNotFoundException::class)]
final class CollectionNotFoundExceptionTest extends TestCase
{
    // Happy path ───────────────────────────────────────────────────

    public function testExtendsRuntimeException(): void
    {
        $this->assertInstanceOf(\RuntimeException::class, new CollectionNotFoundException('articles'));
    }

    public function testMessageContainsCollectionName(): void
    {
        $e = new CollectionNotFoundException('articles');

        $this->assertStringContainsString('articles', $e->getMessage());
    }

    public function testMessageDiffersForDifferentNames(): void
    {
        $a = new CollectionNotFoundException('posts');
        $b = new CollectionNotFoundException('comments');

        $this->assertNotSame($a->getMessage(), $b->getMessage());
    }
}
