<?php

/**
 * @file GetAssetHandlerHandleTest.php
 *
 * Tests for GetAssetHandler::handle().
 *
 * @package App\Assets\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Assets\Application\Query\Handler\Tests;

use App\Assets\Application\DTO\TransformedAssetDto;
use App\Assets\Application\Query\GetAssetQuery;
use App\Assets\Application\Query\Handler\GetAssetHandler;
use App\Files\Domain\Exception\FileNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className: GetAssetHandler::class)]
#[CoversMethod(GetAssetHandler::class, 'handle')]
final class GetAssetHandlerHandleTest extends GetAssetHandlerTest
{
    /** @var string UUID forwarded to the repository in all test scenarios. */
    private const string LOOKUP_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that handle() returns an instance of TransformedAssetDto on success.
     */
    public function testHandleReturnsTransformedAssetDto(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile());

        $result = $this->class->handle(new GetAssetQuery(self::LOOKUP_UUID, null, null, 'contain', 'jpg'));

        $this->assertInstanceOf(TransformedAssetDto::class, $result);
    }

    /**
     * Test that the content field on the returned DTO is a non-empty string.
     */
    public function testHandleDtoContentIsNonEmpty(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile());

        $result = $this->class->handle(new GetAssetQuery(self::LOOKUP_UUID, null, null, 'contain', 'jpg'));

        $this->assertNotEmpty($result->content);
    }

    /**
     * Test that the mimeType on the returned DTO matches the requested output format.
     */
    public function testHandleDtoMimeTypeMatchesRequestedFormat(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile());

        $result = $this->class->handle(new GetAssetQuery(self::LOOKUP_UUID, null, null, 'contain', 'jpg'));

        $this->assertSame('image/jpeg', $result->mimeType);
    }

    /**
     * Test that the filename on the returned DTO matches the file's filenameDownload.
     */
    public function testHandleDtoFilenameMatchesFilenameDownload(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile(filenameDownload: 'my-photo.jpg'));

        $result = $this->class->handle(new GetAssetQuery(self::LOOKUP_UUID, null, null, 'contain', 'jpg'));

        $this->assertSame('my-photo.jpg', $result->filename);
    }

    /**
     * Test that handle() calls findById() exactly once with the query's id value.
     */
    public function testHandlePassesQueryIdToRepository(): void
    {
        $this->fileRepository
            ->expects($this->once())
            ->method('findById')
            ->with(self::LOOKUP_UUID)
            ->willReturn($this->makeFile());

        $this->class->handle(new GetAssetQuery(self::LOOKUP_UUID, null, null, 'contain', 'jpg'));
    }

    // Format branching ─────────────────────────────────────────────

    /**
     * Test that an allowed 'jpg' format produces an image/jpeg MIME type on the DTO.
     */
    public function testHandleForwardsAllowedJpgFormatToTransformer(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile());

        $result = $this->class->handle(new GetAssetQuery(self::LOOKUP_UUID, null, null, 'contain', 'jpg'));

        $this->assertSame('image/jpeg', $result->mimeType);
    }

    /**
     * Test that an allowed 'png' format produces an image/png MIME type on the DTO.
     */
    public function testHandleForwardsAllowedPngFormatToTransformer(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile());

        $result = $this->class->handle(new GetAssetQuery(self::LOOKUP_UUID, null, null, 'contain', 'png'));

        $this->assertSame('image/png', $result->mimeType);
    }

    /**
     * Test that a disallowed format causes handle() to fall back to the file's own MIME type.
     */
    public function testHandleFallsBackToFileMimeWhenFormatIsNotAllowed(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile(type: 'image/jpeg'));

        $result = $this->class->handle(new GetAssetQuery(self::LOOKUP_UUID, null, null, 'contain', 'bmp'));

        $this->assertSame('image/jpeg', $result->mimeType);
    }

    /**
     * Test that a disallowed format with a PNG MIME type produces an image/png DTO.
     */
    public function testHandleFallsBackToPngWhenFormatIsNotAllowedAndMimeIsPng(): void
    {
        $img = imagecreatetruecolor(4, 4);
        imagepng($img, $this->tempDir . '/test-png.png');
        imagedestroy($img);

        $file = $this->makeFile(filenameDisk: 'test-png.png', type: 'image/png');
        $this->fileRepository->method('findById')->willReturn($file);

        $result = $this->class->handle(new GetAssetQuery(self::LOOKUP_UUID, null, null, 'contain', 'invalid'));

        $this->assertSame('image/png', $result->mimeType);
    }

    // Fit branching ────────────────────────────────────────────────

    /**
     * Test that a valid 'contain' fit mode produces a non-empty content result.
     */
    public function testHandleForwardsValidContainFitToTransformer(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile());

        $result = $this->class->handle(new GetAssetQuery(self::LOOKUP_UUID, 8, 8, 'contain', 'jpg'));

        $this->assertNotEmpty($result->content);
    }

    /**
     * Test that a valid 'cover' fit mode produces a non-empty content result.
     */
    public function testHandleForwardsValidCoverFitToTransformer(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile());

        $result = $this->class->handle(new GetAssetQuery(self::LOOKUP_UUID, 8, 8, 'cover', 'jpg'));

        $this->assertNotEmpty($result->content);
    }

    /**
     * Test that a valid 'fill' fit mode produces a non-empty content result.
     */
    public function testHandleForwardsValidFillFitToTransformer(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile());

        $result = $this->class->handle(new GetAssetQuery(self::LOOKUP_UUID, 8, 4, 'fill', 'jpg'));

        $this->assertNotEmpty($result->content);
    }

    /**
     * Test that an unrecognised fit mode defaults to 'contain' and produces a valid result.
     */
    public function testHandleDefaultsToContainWhenFitIsNotAllowed(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile());

        $result = $this->class->handle(new GetAssetQuery(self::LOOKUP_UUID, 8, 8, 'stretch', 'jpg'));

        $this->assertNotEmpty($result->content);
    }

    // Failure / exception paths ────────────────────────────────────

    /**
     * Test that handle() throws FileNotFoundException when the repository returns null.
     */
    public function testHandleThrowsFileNotFoundExceptionWhenRepositoryReturnsNull(): void
    {
        $this->fileRepository->method('findById')->willReturn(null);

        $this->expectException(FileNotFoundException::class);

        $this->class->handle(new GetAssetQuery(self::LOOKUP_UUID, null, null, 'contain', 'jpg'));
    }

    /**
     * Test that handle() throws FileNotFoundException when the physical file does not exist on disk.
     */
    public function testHandleThrowsFileNotFoundExceptionWhenPhysicalFileMissing(): void
    {
        $file = $this->makeFile(filenameDisk: 'no-such-file.jpg');
        $this->fileRepository->method('findById')->willReturn($file);

        $this->expectException(FileNotFoundException::class);

        $this->class->handle(new GetAssetQuery(self::LOOKUP_UUID, null, null, 'contain', 'jpg'));
    }

    /**
     * Test that the FileNotFoundException message contains the queried UUID.
     */
    public function testHandleNotFoundExceptionMessageContainsId(): void
    {
        $this->fileRepository->method('findById')->willReturn(null);

        try {
            $this->class->handle(new GetAssetQuery(self::LOOKUP_UUID, null, null, 'contain', 'jpg'));
            $this->fail('Expected FileNotFoundException was not thrown.');
        } catch (FileNotFoundException $e) {
            $this->assertStringContainsString(self::LOOKUP_UUID, $e->getMessage());
        }
    }
}
