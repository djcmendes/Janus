<?php

/**
 * @file SchemaControllerDiffTest.php
 *
 * Tests for SchemaController::diff().
 *
 * @package App\Schema\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Presentation\Controller\Tests;

use App\Heimdall\Domain\Exception\UnauthorizedException;
use App\Schema\Presentation\Controller\SchemaController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Verifies diff() computes a schema diff and enforces authorization.
 */
#[CoversClass(className: SchemaController::class)]
#[CoversMethod(SchemaController::class, 'diff')]
final class SchemaControllerDiffTest extends SchemaControllerTest
{
    public function testDiffReturns200WithValidSnapshot(): void
    {
        $this->snapshotService->method('snapshot')->willReturn(['version' => 1, 'collections' => [], 'relations' => []]);
        $this->diffService->method('diff')->willReturn([
            'collections' => ['create' => [], 'update' => [], 'delete' => []],
            'fields'      => ['create' => [], 'update' => [], 'delete' => []],
            'relations'   => ['create' => [], 'update' => [], 'delete' => []],
        ]);

        $request    = $this->jsonRequest(['snapshot' => ['version' => 1, 'collections' => [], 'relations' => []]]);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->diff($request, $this->snapshotService, $this->diffService);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testDiffResponseHasDataKey(): void
    {
        $this->snapshotService->method('snapshot')->willReturn(['version' => 1, 'collections' => [], 'relations' => []]);
        $this->diffService->method('diff')->willReturn([
            'collections' => ['create' => [], 'update' => [], 'delete' => []],
            'fields'      => ['create' => [], 'update' => [], 'delete' => []],
            'relations'   => ['create' => [], 'update' => [], 'delete' => []],
        ]);

        $request    = $this->jsonRequest(['snapshot' => ['version' => 1, 'collections' => [], 'relations' => []]]);
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode($controller->diff($request, $this->snapshotService, $this->diffService)->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    public function testDiffReturns422WhenSnapshotKeyMissing(): void
    {
        $request    = $this->jsonRequest(['other' => 'value']);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->diff($request, $this->snapshotService, $this->diffService);

        $this->assertSame(422, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertSame('VALIDATION_ERROR', $body['errors'][0]['extensions']['code']);
    }

    public function testDiffReturns422WhenBodyIsNotArray(): void
    {
        $request = new \Symfony\Component\HttpFoundation\Request(
            server:  ['HTTP_X_CLIENT_TYPE' => 'web'],
            content: 'not-json',
        );
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->diff($request, $this->snapshotService, $this->diffService);

        $this->assertSame(422, $response->getStatusCode());
    }

    public function testDiffReturns422WhenSnapshotIsNotArray(): void
    {
        $request    = $this->jsonRequest(['snapshot' => 'not-an-array']);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->diff($request, $this->snapshotService, $this->diffService);

        $this->assertSame(422, $response->getStatusCode());
    }

    public function testDiffThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $request = $this->jsonRequest(['snapshot' => []]);
        $this->class->diff($request, $this->snapshotService, $this->diffService);
    }

    public function testDiffThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->diff($this->jsonRequest(['snapshot' => []]), $this->snapshotService, $this->diffService);
    }
}
