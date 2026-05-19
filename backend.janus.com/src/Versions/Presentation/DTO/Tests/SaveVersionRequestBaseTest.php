<?php

/**
 * @file SaveVersionRequestBaseTest.php
 *
 * Tests for SaveVersionRequest construction and default property values.
 *
 * @package App\Versions\Presentation\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Presentation\DTO\Tests;

use App\Versions\Presentation\DTO\SaveVersionRequest;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that SaveVersionRequest can be instantiated and that its default
 * property values match the documented contract.
 */
#[CoversClass(className: SaveVersionRequest::class)]
final class SaveVersionRequestBaseTest extends SaveVersionRequestTest
{
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(SaveVersionRequest::class, $this->class);
    }

    public function testCollectionDefaultsToEmptyString(): void
    {
        $this->assertSame('', $this->class->collection);
    }

    public function testItemDefaultsToEmptyString(): void
    {
        $this->assertSame('', $this->class->item);
    }

    public function testKeyDefaultsToMain(): void
    {
        $this->assertSame('main', $this->class->key);
    }

    public function testDataDefaultsToNull(): void
    {
        $this->assertNull($this->class->data);
    }

    public function testDeltaDefaultsToNull(): void
    {
        $this->assertNull($this->class->delta);
    }

    public function testPropertiesCanBeAssigned(): void
    {
        $this->class->collection = 'articles';
        $this->class->item       = 'item-uuid-1';
        $this->class->key        = 'draft';
        $this->class->data       = ['title' => 'Hello'];
        $this->class->delta      = ['title' => ['old' => 'Hi', 'new' => 'Hello']];

        $this->assertSame('articles', $this->class->collection);
        $this->assertSame('item-uuid-1', $this->class->item);
        $this->assertSame('draft', $this->class->key);
        $this->assertSame(['title' => 'Hello'], $this->class->data);
        $this->assertIsArray($this->class->delta);
    }
}
