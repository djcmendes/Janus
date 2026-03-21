<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Component\HttpFoundation\Response;

/**
 * Feature tests for:
 *   GET    /users
 *   GET    /users/:id
 *   POST   /users
 *   PATCH  /users/:id
 *   DELETE /users/:id
 */
final class UsersTest extends ApiTestCase
{
    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        parent::setUp();

        $admin            = $this->createUser('admin@example.com', 'adminpass', ['ROLE_ADMIN']);
        $this->adminToken = $this->getToken($admin);

        $regular         = $this->createUser('user@example.com', 'userpass', ['ROLE_USER']);
        $this->userToken = $this->getToken($regular);
    }

    // ── GET /users ─────────────────────────────────────────────────────────

    public function testListReturnsAllUsers(): void
    {
        $this->apiRequest('GET', '/users', [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('meta', $body);
        $this->assertSame(2, $body['meta']['total_count']);
    }

    public function testListReturns401WithoutToken(): void
    {
        $this->apiRequest('GET', '/users');

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testListReturnsDefaultPagination(): void
    {
        $this->apiRequest('GET', '/users?limit=1&offset=0', [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertCount(1, $body['data']);
        $this->assertSame(2, $body['meta']['total_count']);
    }

    // ── GET /users/:id ─────────────────────────────────────────────────────

    public function testGetByIdReturnsSingleUser(): void
    {
        $user = $this->createUser('target@example.com', 'pass');
        $id   = (string) $user->getId();

        $this->apiRequest('GET', "/users/{$id}", [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame('target@example.com', $body['data']['email']);
    }

    public function testGetByIdReturns404ForUnknownId(): void
    {
        $this->apiRequest('GET', '/users/00000000-0000-0000-0000-000000000000', [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    // ── POST /users ────────────────────────────────────────────────────────

    public function testCreateReturns201WithNewUser(): void
    {
        $this->apiRequest('POST', '/users', [
            'email'    => 'new@example.com',
            'password' => 'newpassword',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame('new@example.com', $body['data']['email']);
        $this->assertArrayHasKey('id', $body['data']);
    }

    public function testCreateReturns409OnDuplicateEmail(): void
    {
        // admin@example.com already exists from setUp
        $this->apiRequest('POST', '/users', [
            'email'    => 'admin@example.com',
            'password' => 'anotherpass',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_CONFLICT, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns403ForNonAdminUser(): void
    {
        $this->apiRequest('POST', '/users', [
            'email'    => 'new@example.com',
            'password' => 'pass',
        ], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns422OnMissingEmail(): void
    {
        $this->apiRequest('POST', '/users', ['password' => 'pass'], $this->adminToken);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    // ── PATCH /users/:id ───────────────────────────────────────────────────

    public function testPatchUpdatesUser(): void
    {
        $user = $this->createUser('patch@example.com', 'pass');
        $id   = (string) $user->getId();

        $this->apiRequest('PATCH', "/users/{$id}", ['first_name' => 'Updated'], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame('Updated', $body['data']['first_name']);
    }

    public function testPatchReturns404ForUnknownUser(): void
    {
        $this->apiRequest('PATCH', '/users/00000000-0000-0000-0000-000000000000', [
            'first_name' => 'Nobody',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    // ── DELETE /users/:id ──────────────────────────────────────────────────

    public function testDeleteReturns204(): void
    {
        $user = $this->createUser('delete@example.com', 'pass');
        $id   = (string) $user->getId();

        $this->apiRequest('DELETE', "/users/{$id}", [], $this->adminToken);

        $this->assertSame(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteReturns404ForUnknownUser(): void
    {
        $this->apiRequest('DELETE', '/users/00000000-0000-0000-0000-000000000000', [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteReturns403ForNonAdminUser(): void
    {
        $user = $this->createUser('victim@example.com', 'pass');
        $id   = (string) $user->getId();

        $this->apiRequest('DELETE', "/users/{$id}", [], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }
}
