<?php

/**
 * @file ServerControllerInfoTest.php
 *
 * Tests for ServerController::info().
 *
 * @package App\Server\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Server\Presentation\Controller\Tests;

use App\Heimdall\Domain\Exception\UnauthorizedException;
use App\Server\Presentation\Controller\ServerController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies info() returns a 200 with the server info payload,
 * and throws when the caller is unauthenticated.
 */
#[CoversClass(className: ServerController::class)]
#[CoversMethod(ServerController::class, 'info')]
final class ServerControllerInfoTest extends ServerControllerTest
{
    public function testInfoReturns200(): void
    {
        $this->assertSame(200, $this->class->info()->getStatusCode());
    }

    public function testInfoResponseHasDataKey(): void
    {
        $body = json_decode((string) $this->class->info()->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    public function testInfoResponseDataHasProjectNameKey(): void
    {
        $body = json_decode((string) $this->class->info()->getContent(), true);

        $this->assertArrayHasKey('project_name', $body['data']);
    }

    public function testInfoResponseDataHasPhpVersionKey(): void
    {
        $body = json_decode((string) $this->class->info()->getContent(), true);

        $this->assertArrayHasKey('php_version', $body['data']);
    }

    public function testInfoThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->info();
    }
}
