<?php

declare(strict_types=1);

namespace App\Extensions\Presentation\Controller\Tests;

use App\Extensions\Presentation\Controller\ExtensionsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: ExtensionsController::class)]
final class ExtensionsControllerGetTest extends ExtensionsControllerTest
{
    public function testGetReturns200WithData(): void
    {
        $extension = $this->makeExtension();
        $this->repository->method('findById')->willReturn($extension);

        $response = $this->class->get($extension->getId(), $this->getExtensionByIdHandler);

        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertArrayHasKey('data', $body);
    }

    public function testGetReturns404WhenNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $response = $this->class->get('nonexistent-id', $this->getExtensionByIdHandler);

        $this->assertSame(404, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    public function testGetThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->get('some-id', $this->getExtensionByIdHandler);
    }
}
