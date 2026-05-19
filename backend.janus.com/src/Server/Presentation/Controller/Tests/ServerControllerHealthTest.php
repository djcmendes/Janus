<?php

/**
 * @file ServerControllerHealthTest.php
 *
 * Tests for ServerController::health().
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
 * Verifies health() wraps the service output in a JSON envelope, returns 503
 * when any service is degraded, and throws when unauthenticated.
 *
 * Note: the 200 path requires all three services to return 'ok'.
 * Redis and RabbitMQ health checks require live infrastructure and are
 * covered by integration tests. The unit tests here exercise the 503 path
 * using the invalid-DSN setup from the base class.
 */
#[CoversClass(className: ServerController::class)]
#[CoversMethod(ServerController::class, 'health')]
final class ServerControllerHealthTest extends ServerControllerTest
{
    public function testHealthReturnsResponse(): void
    {
        $this->connection->method('executeQuery');

        $response = $this->class->health();

        $this->assertNotNull($response);
    }

    public function testHealthResponseHasDataKey(): void
    {
        $this->connection->method('executeQuery');

        $body = json_decode((string) $this->class->health()->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    public function testHealthResponseDataHasDatabaseKey(): void
    {
        $this->connection->method('executeQuery');

        $body = json_decode((string) $this->class->health()->getContent(), true);

        $this->assertArrayHasKey('database', $body['data']);
    }

    public function testHealthResponseDataHasRedisKey(): void
    {
        $this->connection->method('executeQuery');

        $body = json_decode((string) $this->class->health()->getContent(), true);

        $this->assertArrayHasKey('redis', $body['data']);
    }

    public function testHealthResponseDataHasRabbitmqKey(): void
    {
        $this->connection->method('executeQuery');

        $body = json_decode((string) $this->class->health()->getContent(), true);

        $this->assertArrayHasKey('rabbitmq', $body['data']);
    }

    public function testHealthReturns503WhenAnyServiceIsDown(): void
    {
        // With INVALID_DSN (':'), redis/rabbitmq always return error strings → 503
        $this->connection->method('executeQuery');

        $response = $this->class->health();

        $this->assertSame(503, $response->getStatusCode());
    }

    public function testHealthReturns503WhenDatabaseThrows(): void
    {
        $this->connection->method('executeQuery')->willThrowException(new \RuntimeException('DB error'));

        $response = $this->class->health();

        $this->assertSame(503, $response->getStatusCode());
    }

    public function testHealthThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->health();
    }
}
