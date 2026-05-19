<?php

/**
 * @file VersionsControllerGetTest.php
 *
 * Tests for VersionsController::get().
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
 * Verifies get() returns the requested Version or a 404 when missing.
 */
#[CoversClass(className: VersionsController::class)]
#[CoversMethod(VersionsController::class, 'get')]
final class VersionsControllerGetTest extends VersionsControllerTest
{
    public function testGetReturns200WithDataKey(): void
    {
        $version = $this->makeVersion();
        $this->readRepository->method('findById')->willReturn($version);

        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->get($version->getId(), $this->getVersionByIdHandler);

        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $body);
    }

    public function testGetReturns404WhenNotFound(): void
    {
        $this->readRepository->method('findById')->willReturn(null);

        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->get('nonexistent-id', $this->getVersionByIdHandler);

        $this->assertSame(404, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    public function testGetThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $this->class->get('some-id', $this->getVersionByIdHandler);
    }

    public function testGetThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->get('some-id', $this->getVersionByIdHandler);
    }
}
