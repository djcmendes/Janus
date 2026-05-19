<?php

/**
 * @file ServerControllerPingTest.php
 *
 * Tests for ServerController::ping().
 *
 * @package App\Server\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Server\Presentation\Controller\Tests;

use App\Server\Presentation\Controller\ServerController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies ping() returns 200 with {"data":"pong"} for any caller,
 * including unauthenticated ones (PUBLIC scope).
 */
#[CoversClass(className: ServerController::class)]
#[CoversMethod(ServerController::class, 'ping')]
final class ServerControllerPingTest extends ServerControllerTest
{
    public function testPingReturns200(): void
    {
        $response = $this->class->ping();

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testPingResponseHasDataKey(): void
    {
        $body = json_decode((string) $this->class->ping()->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    public function testPingResponseDataIsPong(): void
    {
        $body = json_decode((string) $this->class->ping()->getContent(), true);

        $this->assertSame('pong', $body['data']);
    }

    public function testPingSucceedsWhenUnauthenticated(): void
    {
        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $response   = $controller->ping();

        $this->assertSame(200, $response->getStatusCode());
    }
}
