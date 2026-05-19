<?php

declare(strict_types=1);

namespace App\Fields\Presentation\Controller\Tests;

use App\Fields\Presentation\Controller\FieldsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(className: FieldsController::class)]
final class FieldsControllerListTest extends FieldsControllerTest
{
    public function testListReturns200(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(0);

        $request    = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildController();
        $response   = $controller->list($request);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testListReturnsDataAndMetaKeys(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(0);

        $request    = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildController();
        $response   = $controller->list($request);
        $body       = json_decode((string) $response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('meta', $body);
    }

    public function testListReturnsFieldDtos(): void
    {
        $field = $this->makeFieldMeta();
        $this->repository->method('findPaginated')->willReturn([$field]);
        $this->repository->method('countAll')->willReturn(1);

        $request    = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildController();
        $response   = $controller->list($request);
        $body       = json_decode((string) $response->getContent(), true);

        $this->assertCount(1, $body['data']);
    }

    public function testListThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $request    = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildUnauthenticatedController();
        $controller->list($request);
    }
}
