<?php

declare(strict_types=1);

namespace App\Extensions\Presentation\Controller\Tests;

use App\Extensions\Presentation\Controller\ExtensionsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(ExtensionsController::class)]
final class ExtensionsControllerListTest extends ExtensionsControllerTest
{
    public function testListReturnsOk(): void
    {
        $extension = $this->makeExtension();
        $this->repository->method('findPaginated')->willReturn([$extension]);
        $this->repository->method('countAll')->willReturn(1);

        $request  = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $response = $this->class->list($request, $this->getExtensionsHandler);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testListResponseHasDataAndMetaKeys(): void
    {
        $this->repository->method('findPaginated')->willReturn([$this->makeExtension()]);
        $this->repository->method('countAll')->willReturn(1);

        $request  = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $response = $this->class->list($request, $this->getExtensionsHandler);
        $body     = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('meta', $body);
    }

    public function testListResponseMetaContainsCounts(): void
    {
        $this->repository->method('findPaginated')->willReturn([$this->makeExtension()]);
        $this->repository->method('countAll')->willReturn(1);

        $request  = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $response = $this->class->list($request, $this->getExtensionsHandler);
        $body     = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('total_count', $body['meta']);
        $this->assertArrayHasKey('filter_count', $body['meta']);
    }

    public function testListReturnsEmptyDataWhenNoExtensions(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(0);

        $request  = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $response = $this->class->list($request, $this->getExtensionsHandler);
        $body     = json_decode($response->getContent(), true);

        $this->assertSame([], $body['data']);
    }

    public function testListThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $request    = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->list($request, $this->getExtensionsHandler);
    }
}
