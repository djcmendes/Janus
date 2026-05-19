<?php

/**
 * @file TransformedAssetDtoBaseTest.php
 *
 * Constructor and property compliance tests for TransformedAssetDto.
 *
 * @package App\Assets\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Assets\Application\DTO\Tests;

use App\Assets\Application\DTO\TransformedAssetDto;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: TransformedAssetDto::class)]
final class TransformedAssetDtoBaseTest extends TransformedAssetDtoTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that the SUT is an instance of TransformedAssetDto.
     */
    public function testIsInstanceOfTransformedAssetDto(): void
    {
        $this->assertInstanceOf(TransformedAssetDto::class, $this->class);
    }

    /**
     * Test that the constructor stores the content string on the public property.
     */
    public function testConstructorStoresContent(): void
    {
        $this->assertSame('binary-content', $this->class->content);
    }

    /**
     * Test that the constructor stores the MIME type string on the public property.
     */
    public function testConstructorStoresMimeType(): void
    {
        $this->assertSame('image/jpeg', $this->class->mimeType);
    }

    /**
     * Test that the constructor stores the filename string on the public property.
     */
    public function testConstructorStoresFilename(): void
    {
        $this->assertSame('photo.jpg', $this->class->filename);
    }

    // Edge cases / branching ───────────────────────────────────────

    /**
     * Test that the constructor stores all three fields correctly for a PNG DTO.
     */
    public function testConstructorStoresAllFieldsForPngDto(): void
    {
        $dto = new TransformedAssetDto('png-bytes', 'image/png', 'banner.png');

        $this->assertSame('png-bytes', $dto->content);
        $this->assertSame('image/png', $dto->mimeType);
        $this->assertSame('banner.png', $dto->filename);
    }

    /**
     * Test that all three public properties are declared readonly.
     */
    public function testAllPropertiesAreReadonly(): void
    {
        foreach (['content', 'mimeType', 'filename'] as $property) {
            $this->assertTrue(
                $this->reflection->getProperty($property)->isReadOnly(),
                "Property \${$property} must be readonly.",
            );
        }
    }

    /**
     * Test that all three properties are publicly accessible.
     */
    public function testAllPropertiesArePublic(): void
    {
        foreach (['content', 'mimeType', 'filename'] as $property) {
            $this->assertTrue(
                $this->reflection->getProperty($property)->isPublic(),
                "Property \${$property} must be public.",
            );
        }
    }
}
