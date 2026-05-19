<?php

declare(strict_types=1);

namespace App\Extensions\Presentation\Controller\Tests;

use App\Extensions\Presentation\Controller\ExtensionsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[CoversClass(className: ExtensionsController::class)]
final class ExtensionsControllerPatchTest extends ExtensionsControllerTest
{
    public function testPatchReturns200WhenFound(): void
    {
        $extension = $this->makeExtension();
        $this->repository->method('findById')->willReturn($extension);
        $this->repository->method('save');

        $request    = $this->jsonRequest(['enabled' => true]);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->patch($extension->getId(), $request, $this->updateExtensionHandler);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testPatchReturns404WhenNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $request    = $this->jsonRequest(['enabled' => true]);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->patch('nonexistent-id', $request, $this->updateExtensionHandler);

        $this->assertSame(404, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    public function testPatchThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $request    = $this->jsonRequest(['enabled' => true]);
        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->patch('some-id', $request, $this->updateExtensionHandler);
    }

    public function testPatchThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $request = $this->jsonRequest(['enabled' => true]);
        $this->class->patch('some-id', $request, $this->updateExtensionHandler);
    }
}
