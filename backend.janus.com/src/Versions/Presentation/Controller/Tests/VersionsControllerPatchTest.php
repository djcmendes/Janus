<?php

/**
 * @file VersionsControllerPatchTest.php
 *
 * Tests for VersionsController::patch().
 *
 * @package App\Versions\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Presentation\Controller\Tests;

use App\Heimdall\Domain\Exception\UnauthorizedException;
use App\Versions\Presentation\Controller\VersionsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Verifies patch() updates a Version, returns 404 when missing, and enforces authorization.
 */
#[CoversClass(className: VersionsController::class)]
#[CoversMethod(VersionsController::class, 'patch')]
final class VersionsControllerPatchTest extends VersionsControllerTest
{
    public function testPatchReturns200OnSuccess(): void
    {
        $version = $this->makeVersion();
        $this->serializer->method('deserialize')->willReturn($this->makeUpdateRequest());
        $this->writeRepository->method('findById')->willReturn($version);

        $request    = $this->jsonRequest(['key' => 'draft']);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->patch($version->getId(), $request, $this->updateVersionHandler);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testPatchResponseHasDataKey(): void
    {
        $version = $this->makeVersion();
        $this->serializer->method('deserialize')->willReturn($this->makeUpdateRequest());
        $this->writeRepository->method('findById')->willReturn($version);

        $request    = $this->jsonRequest(['key' => 'draft']);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->patch($version->getId(), $request, $this->updateVersionHandler);
        $body       = json_decode((string) $response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    public function testPatchReturns404WhenNotFound(): void
    {
        $this->serializer->method('deserialize')->willReturn($this->makeUpdateRequest());
        $this->writeRepository->method('findById')->willReturn(null);

        $request    = $this->jsonRequest(['key' => 'draft']);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->patch('nonexistent-id', $request, $this->updateVersionHandler);

        $this->assertSame(404, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    public function testPatchThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $request    = $this->jsonRequest(['key' => 'draft']);
        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->patch('some-id', $request, $this->updateVersionHandler);
    }

    public function testPatchThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $request = $this->jsonRequest(['key' => 'draft']);
        $this->class->patch('some-id', $request, $this->updateVersionHandler);
    }
}
