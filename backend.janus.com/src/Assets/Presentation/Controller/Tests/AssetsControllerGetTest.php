<?php

/**
 * @file AssetsControllerGetTest.php
 *
 * Tests for AssetsController::get().
 *
 * @package App\Assets\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Assets\Presentation\Controller\Tests;

use App\Assets\Presentation\Controller\AssetsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(className: AssetsController::class)]
#[CoversMethod(AssetsController::class, 'get')]
final class AssetsControllerGetTest extends AssetsControllerTest
{
    /** @var string UUID used as the asset identifier in all test scenarios. */
    private const string LOOKUP_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that get() returns HTTP 200 when the asset is found and transformed successfully.
     */
    public function testGetReturnsHttp200ForValidAsset(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile());

        $response = $this->class->get(self::LOOKUP_UUID, new Request(), $this->handler);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Test that the response body contains the binary image data from the transformer.
     */
    public function testGetResponseBodyContainsImageContent(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile());

        $response = $this->class->get(self::LOOKUP_UUID, new Request(), $this->handler);

        $this->assertNotEmpty($response->getContent());
    }

    /**
     * Test that the Content-Type header matches the transformer's output MIME type.
     */
    public function testGetResponseHasCorrectContentTypeHeader(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile());

        $response = $this->class->get(self::LOOKUP_UUID, new Request(), $this->handler);

        $this->assertSame('image/jpeg', $response->headers->get('Content-Type'));
    }

    /**
     * Test that the Content-Disposition header includes the file's original download filename.
     */
    public function testGetResponseHasContentDispositionHeader(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile(filenameDownload: 'landscape.jpg'));

        $response = $this->class->get(self::LOOKUP_UUID, new Request(), $this->handler);

        $this->assertStringContainsString('landscape.jpg', $response->headers->get('Content-Disposition'));
    }

    /**
     * Test that the Cache-Control header is present on a successful response.
     */
    public function testGetResponseHasCacheControlHeader(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile());

        $response = $this->class->get(self::LOOKUP_UUID, new Request(), $this->handler);

        $this->assertNotEmpty($response->headers->get('Cache-Control'));
    }

    /**
     * Test that a 'width' query parameter is forwarded to the handler and accepted.
     */
    public function testGetForwardsWidthQueryParamToHandler(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile());

        $response = $this->class->get(self::LOOKUP_UUID, new Request(query: ['width' => '8']), $this->handler);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Test that a 'height' query parameter is forwarded to the handler and accepted.
     */
    public function testGetForwardsHeightQueryParamToHandler(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile());

        $response = $this->class->get(self::LOOKUP_UUID, new Request(query: ['height' => '4']), $this->handler);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Test that a 'fit' query parameter is forwarded to the handler and accepted.
     */
    public function testGetForwardsFitQueryParamToHandler(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile());

        $response = $this->class->get(self::LOOKUP_UUID, new Request(query: ['fit' => 'cover']), $this->handler);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Test that width=0 is clamped to 1 and the request still succeeds.
     */
    public function testGetClampsWidthToMinimumOfOne(): void
    {
        $this->fileRepository->method('findById')->willReturn($this->makeFile());

        $response = $this->class->get(self::LOOKUP_UUID, new Request(query: ['width' => '0']), $this->handler);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    // Not found ────────────────────────────────────────────────────

    /**
     * Test that get() returns HTTP 404 when no file record exists for the given UUID.
     */
    public function testGetReturnsHttp404WhenAssetNotFound(): void
    {
        $this->fileRepository->method('findById')->willReturn(null);

        $response = $this->class->get(self::LOOKUP_UUID, new Request(), $this->handler);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * Test that the 404 error envelope contains a NOT_FOUND extension code.
     */
    public function testGetNotFoundResponseContainsErrorCode(): void
    {
        $this->fileRepository->method('findById')->willReturn(null);

        $body = json_decode((string) $this->class->get(self::LOOKUP_UUID, new Request(), $this->handler)->getContent(), true);

        $this->assertArrayHasKey('errors', $body);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    /**
     * Test that the 404 error message contains the queried UUID.
     */
    public function testGetNotFoundErrorMessageContainsId(): void
    {
        $this->fileRepository->method('findById')->willReturn(null);

        $body = json_decode((string) $this->class->get(self::LOOKUP_UUID, new Request(), $this->handler)->getContent(), true);

        $this->assertStringContainsString(self::LOOKUP_UUID, $body['errors'][0]['message']);
    }

    // Transform error ──────────────────────────────────────────────

    /**
     * Test that get() returns HTTP 422 when the transformer cannot process the file.
     */
    public function testGetReturnsHttp422WhenTransformFails(): void
    {
        file_put_contents($this->tempDir . '/corrupt.jpg', 'not an image');
        $this->fileRepository->method('findById')->willReturn($this->makeFile(filenameDisk: 'corrupt.jpg'));

        $response = $this->class->get(self::LOOKUP_UUID, new Request(), $this->handler);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    /**
     * Test that the 422 error envelope contains a TRANSFORM_ERROR extension code.
     */
    public function testGetTransformErrorResponseContainsErrorCode(): void
    {
        file_put_contents($this->tempDir . '/corrupt.jpg', 'not an image');
        $this->fileRepository->method('findById')->willReturn($this->makeFile(filenameDisk: 'corrupt.jpg'));

        $body = json_decode((string) $this->class->get(self::LOOKUP_UUID, new Request(), $this->handler)->getContent(), true);

        $this->assertArrayHasKey('errors', $body);
        $this->assertSame('TRANSFORM_ERROR', $body['errors'][0]['extensions']['code']);
    }

    // Guard failures ───────────────────────────────────────────────

    /**
     * Test that get() propagates UnauthorizedException when no authentication token is present.
     */
    public function testGetThrowsWhenAuthenticationFails(): void
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('This endpoint requires authentication.');

        $this->buildControllerWithUnauthenticatedGuard()
             ->get(self::LOOKUP_UUID, new Request(), $this->handler);
    }

    /**
     * Test that get() propagates UnauthorizedException when the client type is not in the allowed list.
     */
    public function testGetThrowsWhenClientIsNotAuthorized(): void
    {
        $this->expectException(UnauthorizedException::class);

        $this->buildControllerWithUnauthorizedClient()
             ->get(self::LOOKUP_UUID, new Request(), $this->handler);
    }
}
