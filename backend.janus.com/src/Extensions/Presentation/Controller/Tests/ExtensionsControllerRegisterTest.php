<?php

declare(strict_types=1);

namespace App\Extensions\Presentation\Controller\Tests;

use App\Extensions\Presentation\Controller\ExtensionsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[CoversClass(ExtensionsController::class)]
final class ExtensionsControllerRegisterTest extends ExtensionsControllerTest
{
    public function testRegisterReturns201OnValidInput(): void
    {
        $this->repository->method('save');

        $request    = $this->jsonRequest(['name' => 'my-hook', 'type' => 'hook', 'version' => '1.0.0']);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->register($request, $this->registerExtensionHandler);

        $this->assertSame(201, $response->getStatusCode());
    }

    public function testRegisterReturns201WithDataKey(): void
    {
        $this->repository->method('save');

        $request    = $this->jsonRequest(['name' => 'my-hook', 'type' => 'hook', 'version' => '1.0.0']);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->register($request, $this->registerExtensionHandler);
        $body       = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    public function testRegisterReturns422WhenNameMissing(): void
    {
        $request    = $this->jsonRequest(['type' => 'hook', 'version' => '1.0.0']);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->register($request, $this->registerExtensionHandler);

        $this->assertSame(422, $response->getStatusCode());
    }

    public function testRegisterReturns422WhenVersionMissing(): void
    {
        $request    = $this->jsonRequest(['name' => 'my-hook', 'type' => 'hook']);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->register($request, $this->registerExtensionHandler);

        $this->assertSame(422, $response->getStatusCode());
    }

    public function testRegisterReturns422OnInvalidType(): void
    {
        $request    = $this->jsonRequest(['name' => 'my-hook', 'type' => 'invalid', 'version' => '1.0.0']);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->register($request, $this->registerExtensionHandler);

        $this->assertSame(422, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertSame('VALIDATION_ERROR', $body['errors'][0]['extensions']['code']);
    }

    public function testRegisterThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $request    = $this->jsonRequest(['name' => 'my-hook', 'type' => 'hook', 'version' => '1.0.0']);
        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->register($request, $this->registerExtensionHandler);
    }

    public function testRegisterThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $request = $this->jsonRequest(['name' => 'my-hook', 'type' => 'hook', 'version' => '1.0.0']);
        $this->class->register($request, $this->registerExtensionHandler);
    }
}
