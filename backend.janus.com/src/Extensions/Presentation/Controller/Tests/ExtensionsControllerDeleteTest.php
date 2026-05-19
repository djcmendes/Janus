<?php

declare(strict_types=1);

namespace App\Extensions\Presentation\Controller\Tests;

use App\Extensions\Presentation\Controller\ExtensionsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[CoversClass(className: ExtensionsController::class)]
final class ExtensionsControllerDeleteTest extends ExtensionsControllerTest
{
    public function testDeleteReturns204WhenFound(): void
    {
        $extension = $this->makeExtension();
        $this->repository->method('findById')->willReturn($extension);
        $this->repository->method('delete');

        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->delete($extension->getId(), $this->deleteExtensionHandler);

        $this->assertSame(204, $response->getStatusCode());
    }

    public function testDeleteReturns404WhenNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->delete('nonexistent-id', $this->deleteExtensionHandler);

        $this->assertSame(404, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    public function testDeleteThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->delete('some-id', $this->deleteExtensionHandler);
    }

    public function testDeleteThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $this->class->delete('some-id', $this->deleteExtensionHandler);
    }
}
