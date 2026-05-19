<?php

/**
 * @file SchemaControllerSnapshotTest.php
 *
 * Tests for SchemaController::snapshot().
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
 * Verifies snapshot() returns the full schema and enforces authorization.
 */
#[CoversClass(className: SchemaController::class)]
#[CoversMethod(SchemaController::class, 'snapshot')]
final class SchemaControllerSnapshotTest extends SchemaControllerTest
{
    public function testSnapshotReturns200(): void
    {
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->snapshot($this->snapshotHandler);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testSnapshotResponseHasDataKey(): void
    {
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode($controller->snapshot($this->snapshotHandler)->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    public function testSnapshotResponseDataMatchesHandlerOutput(): void
    {
        $snapshot = ['version' => 1, 'collections' => [['collection' => 'articles']], 'relations' => []];
        $this->snapshotHandlerReturn = $snapshot;

        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode($controller->snapshot($this->snapshotHandler)->getContent(), true);

        $this->assertSame($snapshot, $body['data']);
    }

    public function testSnapshotThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $this->class->snapshot($this->snapshotHandler);
    }

    public function testSnapshotThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->snapshot($this->snapshotHandler);
    }
}
