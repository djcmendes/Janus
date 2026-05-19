<?php

/**
 * @file CommentDtoToArrayTest.php
 *
 * Tests for CommentDto::toArray().
 *
 * @package App\Comments\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\DTO\Tests;

use App\Comments\Application\DTO\CommentDto;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for CommentDto::toArray() — verifying the serialised representation.
 */
#[CoversClass(className: CommentDto::class)]
#[CoversMethod(CommentDto::class, 'toArray')]
final class CommentDtoToArrayTest extends CommentDtoTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that toArray() includes all expected keys.
     */
    public function testToArrayContainsAllExpectedKeys(): void
    {
        $array = $this->class->toArray();

        foreach (['id', 'collection', 'item', 'comment', 'user', 'created_at', 'updated_at'] as $key) {
            $this->assertArrayHasKey($key, $array);
        }
    }

    /**
     * Test that toArray() maps userId to the 'user' key.
     */
    public function testToArrayMapsUserIdToUserKey(): void
    {
        $array = $this->class->toArray();

        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $array['user']);
        $this->assertArrayNotHasKey('userId', $array);
    }

    /**
     * Test that toArray() returns null for updated_at when never edited.
     */
    public function testToArrayReturnsNullUpdatedAtWhenNeverEdited(): void
    {
        $this->assertNull($this->class->toArray()['updated_at']);
    }
}
