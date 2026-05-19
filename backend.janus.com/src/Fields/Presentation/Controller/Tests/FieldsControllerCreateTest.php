<?php

declare(strict_types=1);

namespace App\Fields\Presentation\Controller\Tests;

use App\Collections\Domain\Entity\CollectionMeta;
use App\Fields\Presentation\Controller\FieldsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[CoversClass(className: FieldsController::class)]
final class FieldsControllerCreateTest extends FieldsControllerTest
{
    private function makeCollectionMeta(): CollectionMeta
    {
        return CollectionMeta::reconstitute(
            id:        'cccccccc-0000-7000-8000-000000000001',
            name:      'articles',
            label:     null,
            icon:      null,
            note:      null,
            hidden:    false,
            singleton: false,
            sortField: null,
            createdAt: new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: null,
        );
    }

    public function testCreateReturns201OnValidInput(): void
    {
        $this->collectionRepository->method('findByName')->willReturn($this->makeCollectionMeta());
        $this->repository->method('findByCollectionAndField')->willReturn(null);
        $this->repository->method('save');
        $this->connection->method('executeStatement')->willReturn(1);

        $request    = $this->jsonRequest(['field' => 'slug', 'type' => 'string']);
        $controller = $this->buildAdminController();
        $response   = $controller->create('articles', $request);

        $this->assertSame(201, $response->getStatusCode());
    }

    public function testCreateReturns201WithDataKey(): void
    {
        $this->collectionRepository->method('findByName')->willReturn($this->makeCollectionMeta());
        $this->repository->method('findByCollectionAndField')->willReturn(null);
        $this->repository->method('save');
        $this->connection->method('executeStatement')->willReturn(1);

        $request    = $this->jsonRequest(['field' => 'slug', 'type' => 'string']);
        $controller = $this->buildAdminController();
        $response   = $controller->create('articles', $request);
        $body       = json_decode((string) $response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    public function testCreateReturns422WhenFieldMissing(): void
    {
        $request    = $this->jsonRequest(['type' => 'string']);
        $controller = $this->buildAdminController();
        $response   = $controller->create('articles', $request);

        $this->assertSame(422, $response->getStatusCode());
    }

    public function testCreateReturns422WhenTypeMissing(): void
    {
        $request    = $this->jsonRequest(['field' => 'slug']);
        $controller = $this->buildAdminController();
        $response   = $controller->create('articles', $request);

        $this->assertSame(422, $response->getStatusCode());
    }

    public function testCreateReturns422WhenTypeInvalid(): void
    {
        $request    = $this->jsonRequest(['field' => 'slug', 'type' => 'invalid']);
        $controller = $this->buildAdminController();
        $response   = $controller->create('articles', $request);

        $this->assertSame(422, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('VALIDATION_ERROR', $body['errors'][0]['extensions']['code']);
    }

    public function testCreateReturns404WhenCollectionNotFound(): void
    {
        $this->collectionRepository->method('findByName')->willReturn(null);

        $request    = $this->jsonRequest(['field' => 'slug', 'type' => 'string']);
        $controller = $this->buildAdminController();
        $response   = $controller->create('nonexistent', $request);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testCreateReturns409WhenFieldAlreadyExists(): void
    {
        $this->collectionRepository->method('findByName')->willReturn($this->makeCollectionMeta());
        $this->repository->method('findByCollectionAndField')->willReturn($this->makeFieldMeta());

        $request    = $this->jsonRequest(['field' => 'title', 'type' => 'string']);
        $controller = $this->buildAdminController();
        $response   = $controller->create('articles', $request);

        $this->assertSame(409, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('FIELD_EXISTS', $body['errors'][0]['extensions']['code']);
    }

    public function testCreateThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $request    = $this->jsonRequest(['field' => 'slug', 'type' => 'string']);
        $controller = $this->buildUnauthenticatedController();
        $controller->create('articles', $request);
    }

    public function testCreateThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $request    = $this->jsonRequest(['field' => 'slug', 'type' => 'string']);
        $controller = $this->buildController(authenticated: true, admin: false);
        $controller->create('articles', $request);
    }
}
