<?php

declare(strict_types=1);

namespace App\Fields\Presentation\Controller\Tests;

use App\Fields\Presentation\Controller\FieldsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[CoversClass(className: FieldsController::class)]
final class FieldsControllerDeleteTest extends FieldsControllerTest
{
    public function testDeleteReturns204WhenFound(): void
    {
        $field = $this->makeFieldMeta();
        $this->repository->method('findByCollectionAndField')->willReturn($field);
        $this->repository->method('delete');
        $this->connection->method('executeStatement')->willReturn(1);

        $controller = $this->buildAdminController();
        $response   = $controller->delete('articles', 'title');

        $this->assertSame(204, $response->getStatusCode());
    }

    public function testDeleteReturns404WhenNotFound(): void
    {
        $this->repository->method('findByCollectionAndField')->willReturn(null);

        $controller = $this->buildAdminController();
        $response   = $controller->delete('articles', 'nonexistent');

        $this->assertSame(404, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    public function testDeleteThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildUnauthenticatedController();
        $controller->delete('articles', 'title');
    }

    public function testDeleteThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $controller = $this->buildController(authenticated: true, admin: false);
        $controller->delete('articles', 'title');
    }
}
