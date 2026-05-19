<?php

/**
 * @file AssetTransformServiceTransformTest.php
 *
 * Tests for AssetTransformService::transform().
 *
 * @package App\Assets\Domain\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Assets\Domain\Service\Tests;

use App\Assets\Domain\Service\AssetTransformService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className: AssetTransformService::class)]
#[CoversMethod(AssetTransformService::class, 'transform')]
final class AssetTransformServiceTransformTest extends AssetTransformServiceTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that transform() returns an array containing a 'content' key.
     */
    public function testTransformReturnsArrayWithContentKey(): void
    {
        $result = $this->class->transform($this->tempJpegPath, 'image/jpeg', null, null);

        $this->assertArrayHasKey('content', $result);
    }

    /**
     * Test that transform() returns an array containing a 'mimeType' key.
     */
    public function testTransformReturnsArrayWithMimeTypeKey(): void
    {
        $result = $this->class->transform($this->tempJpegPath, 'image/jpeg', null, null);

        $this->assertArrayHasKey('mimeType', $result);
    }

    /**
     * Test that the content value is a non-empty binary string.
     */
    public function testTransformContentIsNonEmptyString(): void
    {
        $result = $this->class->transform($this->tempJpegPath, 'image/jpeg', null, null);

        $this->assertIsString($result['content']);
        $this->assertNotEmpty($result['content']);
    }

    /**
     * Test that format='jpg' produces a 'image/jpeg' MIME type in the result.
     */
    public function testTransformJpgFormatReturnsMimeTypeJpeg(): void
    {
        $result = $this->class->transform($this->tempJpegPath, 'image/jpeg', null, null, 'contain', 'jpg');

        $this->assertSame('image/jpeg', $result['mimeType']);
    }

    /**
     * Test that format='png' produces an 'image/png' MIME type in the result.
     */
    public function testTransformPngFormatReturnsMimeTypePng(): void
    {
        $result = $this->class->transform($this->tempJpegPath, 'image/jpeg', null, null, 'contain', 'png');

        $this->assertSame('image/png', $result['mimeType']);
    }

    /**
     * Test that format='webp' produces an 'image/webp' MIME type in the result.
     */
    public function testTransformWebpFormatReturnsMimeTypeWebp(): void
    {
        $result = $this->class->transform($this->tempJpegPath, 'image/jpeg', null, null, 'contain', 'webp');

        $this->assertSame('image/webp', $result['mimeType']);
    }

    /**
     * Test that the default format (no format argument) produces 'image/jpeg' MIME type.
     */
    public function testTransformDefaultFormatIsJpeg(): void
    {
        $result = $this->class->transform($this->tempJpegPath, 'image/jpeg', null, null);

        $this->assertSame('image/jpeg', $result['mimeType']);
    }

    /**
     * Test that a PNG source file can be loaded and transformed without errors.
     */
    public function testTransformCanLoadPngSource(): void
    {
        $result = $this->class->transform($this->tempPngPath, 'image/png', null, null, 'contain', 'png');

        $this->assertNotEmpty($result['content']);
        $this->assertSame('image/png', $result['mimeType']);
    }

    // Dimension / fit branching ────────────────────────────────────

    /**
     * Test that passing null for both width and height preserves the source image dimensions.
     */
    public function testTransformNullWidthAndHeightPreservesOriginalDimensions(): void
    {
        $result           = $this->class->transform($this->tempJpegPath, 'image/jpeg', null, null);
        [$width, $height] = getimagesizefromstring($result['content']);

        $this->assertSame(16, $width);
        $this->assertSame(9, $height);
    }

    /**
     * Test that providing only a width produces a non-empty output with a proportional height.
     */
    public function testTransformWithOnlyWidthProducesOutput(): void
    {
        $result = $this->class->transform($this->tempJpegPath, 'image/jpeg', 8, null);

        $this->assertNotEmpty($result['content']);
    }

    /**
     * Test that providing only a height produces a non-empty output with a proportional width.
     */
    public function testTransformWithOnlyHeightProducesOutput(): void
    {
        $result = $this->class->transform($this->tempJpegPath, 'image/jpeg', null, 4);

        $this->assertNotEmpty($result['content']);
    }

    /**
     * Test that fit='contain' produces output dimensions entirely within the target bounds.
     */
    public function testTransformFitContainDoesNotExceedTargetBounds(): void
    {
        $result           = $this->class->transform($this->tempJpegPath, 'image/jpeg', 8, 8, 'contain');
        [$width, $height] = getimagesizefromstring($result['content']);

        $this->assertLessThanOrEqual(8, $width);
        $this->assertLessThanOrEqual(8, $height);
    }

    /**
     * Test that fit='cover' produces output at exactly the requested dimensions.
     */
    public function testTransformFitCoverProducesExactTargetDimensions(): void
    {
        $result           = $this->class->transform($this->tempJpegPath, 'image/jpeg', 8, 8, 'cover');
        [$width, $height] = getimagesizefromstring($result['content']);

        $this->assertSame(8, $width);
        $this->assertSame(8, $height);
    }

    /**
     * Test that fit='fill' stretches the image to exactly the requested dimensions.
     */
    public function testTransformFitFillProducesExactTargetDimensions(): void
    {
        $result           = $this->class->transform($this->tempJpegPath, 'image/jpeg', 8, 4, 'fill');
        [$width, $height] = getimagesizefromstring($result['content']);

        $this->assertSame(8, $width);
        $this->assertSame(4, $height);
    }

    /**
     * Test that fit='contain' with only a width constraint produces a proportional height.
     */
    public function testTransformFitContainWithOnlyWidthProducesProportionalHeight(): void
    {
        $result           = $this->class->transform($this->tempJpegPath, 'image/jpeg', 8, null, 'contain');
        [$width, $height] = getimagesizefromstring($result['content']);

        $this->assertSame(8, $width);
        $this->assertGreaterThan(0, $height);
    }

    /**
     * Test that fit='contain' with only a height constraint produces a proportional width.
     */
    public function testTransformFitContainWithOnlyHeightProducesProportionalWidth(): void
    {
        $result           = $this->class->transform($this->tempJpegPath, 'image/jpeg', null, 4, 'contain');
        [$width, $height] = getimagesizefromstring($result['content']);

        $this->assertGreaterThan(0, $width);
        $this->assertSame(4, $height);
    }

    /**
     * Test that an unrecognised fit mode falls through to the 'contain' default behaviour.
     */
    public function testTransformUnknownFitDefaultsToContainBehaviour(): void
    {
        $result           = $this->class->transform($this->tempJpegPath, 'image/jpeg', 8, 8, 'stretch');
        [$width, $height] = getimagesizefromstring($result['content']);

        $this->assertLessThanOrEqual(8, $width);
        $this->assertLessThanOrEqual(8, $height);
    }

    // Failure / exception paths ────────────────────────────────────

    /**
     * Test that transform() throws RuntimeException when the source path does not exist.
     */
    public function testTransformThrowsRuntimeExceptionForNonExistentSourcePath(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->class->transform('/nonexistent/path/image.jpg', 'image/jpeg', null, null);
    }

    /**
     * Test that transform() throws RuntimeException when the MIME type has no matching GD loader.
     */
    public function testTransformThrowsRuntimeExceptionForUnsupportedMimeType(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->class->transform($this->tempJpegPath, 'application/pdf', null, null);
    }

    /**
     * Test that transform() throws RuntimeException when GD cannot decode the file bytes.
     */
    public function testTransformThrowsRuntimeExceptionWhenGdCannotDecodeCorruptFile(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->class->transform($this->corruptFilePath, 'image/jpeg', null, null);
    }
}
