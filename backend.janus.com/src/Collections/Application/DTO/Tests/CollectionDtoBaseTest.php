<?php

/**
 * @file CollectionDtoBaseTest.php
 *
 * Constructor and property compliance tests for CollectionDto.
 *
 * @package App\Collections\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\DTO\Tests;

use App\Collections\Application\DTO\CollectionDto;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: CollectionDto::class)]
final class CollectionDtoBaseTest extends CollectionDtoTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testIsInstanceOfCollectionDto(): void
    {
        $this->assertInstanceOf(CollectionDto::class, $this->class);
    }

    public function testIdIsString(): void
    {
        $this->assertIsString($this->class->id);
    }

    public function testNameIsArticles(): void
    {
        $this->assertSame('articles', $this->class->name);
    }

    public function testLabelIsSet(): void
    {
        $this->assertSame('Articles', $this->class->label);
    }

    public function testIconIsSet(): void
    {
        $this->assertSame('mdi-file-document', $this->class->icon);
    }

    public function testNoteIsSet(): void
    {
        $this->assertSame('Main blog articles collection.', $this->class->note);
    }

    public function testHiddenIsFalse(): void
    {
        $this->assertFalse($this->class->hidden);
    }

    public function testSingletonIsFalse(): void
    {
        $this->assertFalse($this->class->singleton);
    }

    public function testSortFieldIsSet(): void
    {
        $this->assertSame('sort', $this->class->sortField);
    }

    public function testCreatedAtIsString(): void
    {
        $this->assertIsString($this->class->createdAt);
    }
}
