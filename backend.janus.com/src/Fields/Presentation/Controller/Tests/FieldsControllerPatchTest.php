<?php

declare(strict_types=1);

namespace App\Fields\Presentation\Controller\Tests;

use App\Fields\Presentation\Controller\FieldsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[CoversClass(FieldsController::class)]
final class FieldsControllerPatchTest extends FieldsControllerTest
{
    public function testPatchReturns200WhenFound(): void
    {
        $field = $this->makeFieldMeta();
        $this->repository->method('findByCollectionAndField')->willReturn($field);
        $this->repository->method('save');

        $request    = $this->jsonRequest(['label' => 'New Label']);
        $controller = $this->buildAdminController();
        $response   = $controller->patch('articles', 'title', $request);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testPatchReturns404WhenNotFound(): void
    {
        $this->repository->method('findByCollectionAndField')->willReturn(null);

        $request    = $this->jsonRequest(['label' => 'New Label']);
        $controller = $this->buildAdminController();
        $response   = $controller->patch('articles', 'nonexistent', $request);

        $this->assertSame(404, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    public function testPatchThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $request    = $this->jsonRequest(['label' => 'New Label']);
        $controller = $this->buildUnauthenticatedController();
        $controller->patch('articles', 'title', $request);
    }

    public function testPatchThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $request    = $this->jsonRequest(['label' => 'New Label']);
        $controller = $this->buildController(authenticated: true, admin: false);
        $controller->patch('articles', 'title', $request);
    }
}
