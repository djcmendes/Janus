<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Component\HttpFoundation\Response;

/**
 * Feature tests for:
 *   GET    /roles
 *   POST   /roles
 *   GET    /roles/{id}
 *   PATCH  /roles/{id}
 *   DELETE /roles/{id}
 */
final class RolesTest extends ApiTestCase
{
    // ── GET /roles ─────────────────────────────────────────────────────────

    public function testGetRolesRequiresAuth(): void
    {
        $this->apiRequest('GET', '/roles');
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testGetRolesReturnsEmptyCollection(): void
    {
        $user  = $this->createUser('admin@example.com', 'password123', ['ROLE_ADMIN']);
        $token = $this->getToken($user);

        $this->apiRequest('GET', '/roles', [], $token);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertIsArray($body['data']);
    }

    // ── POST /roles ─────────────────────────────────────────────────────────

    public function testCreateRoleReturns201(): void
    {
        $user  = $this->createUser('admin@example.com', 'password123', ['ROLE_ADMIN']);
        $token = $this->getToken($user);

        $this->apiRequest('POST', '/roles', ['name' => 'editors'], $token);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $body = $this->responseJson();
        $this->assertSame('editors', $body['data']['name']);
    }

    public function testCreateRoleRequiresAuth(): void
    {
        $this->apiRequest('POST', '/roles', ['name' => 'editors']);
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    // ── GET /roles/{id} ─────────────────────────────────────────────────────

    public function testGetRoleByIdReturns404ForUnknown(): void
    {
        $user  = $this->createUser('admin@example.com', 'password123', ['ROLE_ADMIN']);
        $token = $this->getToken($user);

        $this->apiRequest('GET', '/roles/00000000-0000-0000-0000-000000000000', [], $token);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testGetRoleByIdReturnsSavedRole(): void
    {
        $user  = $this->createUser('admin@example.com', 'password123', ['ROLE_ADMIN']);
        $token = $this->getToken($user);

        $this->apiRequest('POST', '/roles', ['name' => 'writers'], $token);
        $created = $this->responseJson();
        $id = $created['data']['id'];

        $this->apiRequest('GET', '/roles/' . $id, [], $token);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('writers', $this->responseJson()['data']['name']);
    }

    // ── PATCH /roles/{id} ──────────────────────────────────────────────────

    public function testPatchRoleUpdatesName(): void
    {
        $user  = $this->createUser('admin@example.com', 'password123', ['ROLE_ADMIN']);
        $token = $this->getToken($user);

        $this->apiRequest('POST', '/roles', ['name' => 'writers'], $token);
        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('PATCH', '/roles/' . $id, ['name' => 'senior-writers'], $token);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('senior-writers', $this->responseJson()['data']['name']);
    }

    // ── DELETE /roles/{id} ─────────────────────────────────────────────────

    public function testDeleteRoleReturns204(): void
    {
        $user  = $this->createUser('admin@example.com', 'password123', ['ROLE_ADMIN']);
        $token = $this->getToken($user);

        $this->apiRequest('POST', '/roles', ['name' => 'to-delete'], $token);
        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('DELETE', '/roles/' . $id, [], $token);

        $this->assertSame(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }
}
