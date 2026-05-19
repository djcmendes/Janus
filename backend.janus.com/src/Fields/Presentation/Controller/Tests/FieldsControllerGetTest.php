<?php

declare(strict_types=1);

namespace App\Fields\Presentation\Controller\Tests;

use App\Fields\Presentation\Controller\FieldsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: FieldsController::class)]
final class FieldsControllerGetTest extends FieldsControllerTest
{
    public function testGetReturns200WhenFound(): void
    {
        $field = $this->makeFieldMeta();
        $this->repository->method('findByCollectionAndField')->willReturn($field);

        $controller = $this->buildController();
        $response   = $controller->get('articles', 'title');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetReturnsDataKey(): void
    {
        $field = $this->makeFieldMeta();
        $this->repository->method('findByCollectionAndField')->willReturn($field);

        $controller = $this->buildController();
        $response   = $controller->get('articles', 'title');
        $body       = json_decode((string) $response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    public function testGetReturns404WhenNotFound(): void
    {
        $this->repository->method('findByCollectionAndField')->willReturn(null);

        $controller = $this->buildController();
        $response   = $controller->get('articles', 'nonexistent');

        $this->assertSame(404, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    public function testGetThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildUnauthenticatedController();
        $controller->get('articles', 'title');
    }
}
