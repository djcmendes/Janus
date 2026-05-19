<?php

/**
 * @file VersionsControllerDeleteTest.php
 *
 * Tests for VersionsController::delete().
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
 * Verifies delete() removes a Version and enforces authorization.
 */
#[CoversClass(className: VersionsController::class)]
#[CoversMethod(VersionsController::class, 'delete')]
final class VersionsControllerDeleteTest extends VersionsControllerTest
{
    public function testDeleteReturns204WhenFound(): void
    {
        $version = $this->makeVersion();
        $this->writeRepository->method('findById')->willReturn($version);

        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->delete($version->getId(), $this->deleteVersionHandler);

        $this->assertSame(204, $response->getStatusCode());
    }

    public function testDeleteReturns404WhenNotFound(): void
    {
        $this->writeRepository->method('findById')->willReturn(null);

        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->delete('nonexistent-id', $this->deleteVersionHandler);

        $this->assertSame(404, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    public function testDeleteThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->delete('some-id', $this->deleteVersionHandler);
    }

    public function testDeleteThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $this->class->delete('some-id', $this->deleteVersionHandler);
    }
}
