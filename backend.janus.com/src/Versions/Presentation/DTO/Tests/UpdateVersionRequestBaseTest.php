<?php

/**
 * @file UpdateVersionRequestBaseTest.php
 *
 * Tests for UpdateVersionRequest construction and default property values.
 *
 * @package App\Versions\Presentation\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Presentation\DTO\Tests;

use App\Versions\Presentation\DTO\UpdateVersionRequest;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that UpdateVersionRequest can be instantiated and that its UNCHANGED
 * sentinel defaults are set correctly before the serializer populates the fields.
 */
#[CoversClass(className: UpdateVersionRequest::class)]
final class UpdateVersionRequestBaseTest extends UpdateVersionRequestTest
{
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(UpdateVersionRequest::class, $this->class);
    }

    public function testKeyDefaultsToUnchangedSentinel(): void
    {
        $this->assertSame('__UNCHANGED__', $this->class->key);
    }

    public function testDeltaDefaultsToUnchangedSentinel(): void
    {
        $this->assertSame('__UNCHANGED__', $this->class->delta);
    }

    public function testKeyCanBeAssigned(): void
    {
        $this->class->key = 'draft';

        $this->assertSame('draft', $this->class->key);
    }

    public function testDeltaCanBeAssigned(): void
    {
        $this->class->delta = ['title' => ['old' => 'Hi', 'new' => 'Hello']];

        $this->assertIsArray($this->class->delta);
    }
}
