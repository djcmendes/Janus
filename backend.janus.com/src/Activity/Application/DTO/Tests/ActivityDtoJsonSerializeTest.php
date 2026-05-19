<?php

/**
 * @file ActivityDtoJsonSerializeTest.php
 *
 * Tests for ActivityDto::jsonSerialize().
 *
 * @package App\Activity\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\DTO\Tests;

use App\Activity\Application\DTO\ActivityDto;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that jsonSerialize() satisfies the JsonSerializable contract and
 * produces output identical to toArray() so json_encode() works correctly.
 */
#[CoversClass(className:  ActivityDto::class)]
#[CoversMethod(className: ActivityDto::class, methodName: 'jsonSerialize')]
final class ActivityDtoJsonSerializeTest extends ActivityDtoTest
{
    /**
     * Test that jsonSerialize() returns an array.
     */
    public function testJsonSerializeReturnsArray(): void
    {
        $this->assertIsArray(actual: $this->class->jsonSerialize());
    }

    /**
     * Test that jsonSerialize() returns the same data as toArray().
     */
    public function testJsonSerializeMatchesToArray(): void
    {
        $this->assertSame(
            expected: $this->class->toArray(),
            actual:   $this->class->jsonSerialize(),
        );
    }

    /**
     * Test that json_encode() produces valid JSON from the DTO.
     */
    public function testJsonEncodeProducesValidJson(): void
    {
        $json = json_encode(value: $this->class);

        $this->assertIsString(actual: $json);
        $this->assertJson(actual: $json);
    }

    /**
     * Test that json_encode() round-trips all eight expected keys.
     */
    public function testJsonEncodeContainsAllExpectedKeys(): void
    {
        $decoded = json_decode(json: json_encode(value: $this->class), associative: true);

        foreach (['id', 'action', 'collection', 'item', 'user', 'ip', 'user_agent', 'timestamp'] as $key) {
            $this->assertArrayHasKey(key: $key, array: $decoded);
        }
    }

    /**
     * Test that json_encode() preserves scalar field values correctly.
     */
    public function testJsonEncodeValuesMatchDtoProperties(): void
    {
        $decoded = json_decode(json: json_encode(value: $this->class), associative: true);

        $this->assertSame(expected: $this->class->id,        actual: $decoded['id']);
        $this->assertSame(expected: $this->class->action,    actual: $decoded['action']);
        $this->assertSame(expected: $this->class->timestamp, actual: $decoded['timestamp']);
    }
}
