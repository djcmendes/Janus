<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Component\HttpFoundation\Response;

/**
 * Feature tests for:
 *   GET /activity
 *   GET /activity/{id}
 */
final class ActivityTest extends ApiTestCase
{
    // ── GET /activity ──────────────────────────────────────────────────────

    public function testGetActivityRequiresAuth(): void
    {
        $this->apiRequest('GET', '/activity');
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testGetActivityReturnsCollection(): void
    {
        $user  = $this->createUser('admin@example.com', 'password123', ['ROLE_ADMIN']);
        $token = $this->getToken($user);

        $this->apiRequest('GET', '/activity', [], $token);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertIsArray($body['data']);
        $this->assertArrayHasKey('meta', $body);
    }

    public function testGetActivityHasPaginationMeta(): void
    {
        $user  = $this->createUser('admin@example.com', 'password123', ['ROLE_ADMIN']);
        $token = $this->getToken($user);

        $this->apiRequest('GET', '/activity', [], $token);

        $meta = $this->responseJson()['meta'];
        $this->assertArrayHasKey('total_count', $meta);
        $this->assertArrayHasKey('filter_count', $meta);
    }

    // ── GET /activity/{id} ─────────────────────────────────────────────────

    public function testGetActivityByIdReturns404ForUnknown(): void
    {
        $user  = $this->createUser('admin@example.com', 'password123', ['ROLE_ADMIN']);
        $token = $this->getToken($user);

        $this->apiRequest('GET', '/activity/00000000-0000-0000-0000-000000000000', [], $token);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }
}
