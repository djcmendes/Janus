<?php

declare(strict_types=1);

namespace App\Fields\Presentation\Controller\Tests;

use App\Fields\Presentation\Controller\FieldsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FieldsController::class)]
final class FieldsControllerListByCollectionTest extends FieldsControllerTest
{
    public function testListByCollectionReturns200(): void
    {
        $this->repository->method('findByCollection')->willReturn([]);

        $controller = $this->buildController();
        $response   = $controller->listByCollection('articles');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testListByCollectionReturnsDataAndMetaKeys(): void
    {
        $this->repository->method('findByCollection')->willReturn([]);

        $controller = $this->buildController();
        $response   = $controller->listByCollection('articles');
        $body       = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('meta', $body);
    }

    public function testListByCollectionReturnsMappedFields(): void
    {
        $field = $this->makeFieldMeta();
        $this->repository->method('findByCollection')->willReturn([$field]);

        $controller = $this->buildController();
        $response   = $controller->listByCollection('articles');
        $body       = json_decode($response->getContent(), true);

        $this->assertCount(1, $body['data']);
    }

    public function testListByCollectionThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildUnauthenticatedController();
        $controller->listByCollection('articles');
    }
}
