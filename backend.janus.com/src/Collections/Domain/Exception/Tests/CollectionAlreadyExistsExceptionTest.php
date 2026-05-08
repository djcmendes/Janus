<?php

/**
 * @file CollectionAlreadyExistsExceptionTest.php
 *
 * Tests for CollectionAlreadyExistsException.
 *
 * @package App\Collections\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Domain\Exception\Tests;

use App\Collections\Domain\Exception\CollectionAlreadyExistsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CollectionAlreadyExistsException::class)]
final class CollectionAlreadyExistsExceptionTest extends TestCase
{
    // Happy path ───────────────────────────────────────────────────

    public function testExtendsRuntimeException(): void
    {
        $this->assertInstanceOf(\RuntimeException::class, new CollectionAlreadyExistsException('articles'));
    }

    public function testMessageContainsCollectionName(): void
    {
        $e = new CollectionAlreadyExistsException('articles');

        $this->assertStringContainsString('articles', $e->getMessage());
    }

    public function testMessageDiffersForDifferentNames(): void
    {
        $a = new CollectionAlreadyExistsException('posts');
        $b = new CollectionAlreadyExistsException('users');

        $this->assertNotSame($a->getMessage(), $b->getMessage());
    }
}
