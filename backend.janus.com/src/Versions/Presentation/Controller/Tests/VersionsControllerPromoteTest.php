<?php

/**
 * @file VersionsControllerPromoteTest.php
 *
 * Tests for VersionsController::promote().
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
 * Verifies promote() applies a version snapshot, returns 404 when missing, and
 * returns 422 on a promotion runtime failure.
 */
#[CoversClass(className: VersionsController::class)]
#[CoversMethod(VersionsController::class, 'promote')]
final class VersionsControllerPromoteTest extends VersionsControllerTest
{
    public function testPromoteReturns200OnSuccess(): void
    {
        $version = $this->makeVersion();
        $this->writeRepository->method('findById')->willReturn($version);
        $this->connection->method('executeStatement')->willReturn(1);

        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->promote($version->getId(), $this->promoteVersionHandler);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testPromoteResponseHasDataKey(): void
    {
        $version = $this->makeVersion();
        $this->writeRepository->method('findById')->willReturn($version);
        $this->connection->method('executeStatement')->willReturn(1);

        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->promote($version->getId(), $this->promoteVersionHandler);
        $body       = json_decode((string) $response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    public function testPromoteReturns404WhenNotFound(): void
    {
        $this->writeRepository->method('findById')->willReturn(null);

        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->promote('nonexistent-id', $this->promoteVersionHandler);

        $this->assertSame(404, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    public function testPromoteReturns422WhenItemNotFound(): void
    {
        $version = $this->makeVersion();
        $this->writeRepository->method('findById')->willReturn($version);
        $this->connection->method('executeStatement')->willReturn(0);

        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->promote($version->getId(), $this->promoteVersionHandler);

        $this->assertSame(422, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('PROMOTE_ERROR', $body['errors'][0]['extensions']['code']);
    }

    public function testPromoteThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->promote('some-id', $this->promoteVersionHandler);
    }

    public function testPromoteThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $this->class->promote('some-id', $this->promoteVersionHandler);
    }
}
