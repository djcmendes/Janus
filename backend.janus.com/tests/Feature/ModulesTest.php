<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Component\HttpFoundation\Response;

/**
 * Feature tests for:
 *   GET    /modules
 *   GET    /modules/:id
 *   POST   /modules
 *   PATCH  /modules/:id
 *   DELETE /modules/:id
 */
final class ModulesTest extends ApiTestCase
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

    // ── GET /modules ───────────────────────────────────────────────────────

    public function testListReturnsEmptyDataWithMeta(): void
    {
        $this->apiRequest('GET', '/modules', [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('meta', $body);
        $this->assertSame(0, $body['meta']['total_count']);
        $this->assertSame(0, $body['meta']['filter_count']);
    }

    public function testListReturns401WithoutToken(): void
    {
        $this->apiRequest('GET', '/modules');

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testListRespectsPaginationParams(): void
    {
        $this->apiRequest('POST', '/modules', [
            'type' => 'menu',
            'name' => 'Module Alpha',
        ], $this->adminToken);

        $this->apiRequest('POST', '/modules', [
            'type' => 'html',
            'name' => 'Module Beta',
        ], $this->adminToken);

        $this->apiRequest('GET', '/modules?limit=1&offset=0', [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertCount(1, $body['data']);
        $this->assertSame(2, $body['meta']['total_count']);
        $this->assertSame(1, $body['meta']['filter_count']);
    }

    // ── POST /modules ──────────────────────────────────────────────────────

    public function testCreateReturns201WithNewModule(): void
    {
        $this->apiRequest('POST', '/modules', [
            'type'   => 'menu',
            'name'   => 'Main Navigation',
            'config' => ['items' => []],
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertSame('menu', $body['data']['type']);
        $this->assertSame('Main Navigation', $body['data']['name']);
        $this->assertArrayHasKey('id', $body['data']);
        $this->assertArrayHasKey('created_at', $body['data']);
        $this->assertNull($body['data']['updated_at']);
    }

    public function testCreateReturns422OnMissingType(): void
    {
        $this->apiRequest('POST', '/modules', [
            'name' => 'No Type Module',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns422OnMissingName(): void
    {
        $this->apiRequest('POST', '/modules', [
            'type' => 'html',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns422OnInvalidType(): void
    {
        $this->apiRequest('POST', '/modules', [
            'type' => 'invalid_type',
            'name' => 'Bad Module',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns403ForNonAdminUser(): void
    {
        $this->apiRequest('POST', '/modules', [
            'type' => 'html',
            'name' => 'Forbidden Module',
        ], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    // ── GET /modules/:id ───────────────────────────────────────────────────

    public function testGetByIdReturnsSingleModule(): void
    {
        $this->apiRequest('POST', '/modules', [
            'type' => 'search',
            'name' => 'Fetchable Module',
        ], $this->adminToken);

        $created = $this->responseJson();
        $id      = $created['data']['id'];

        $this->apiRequest('GET', "/modules/{$id}", [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame($id, $body['data']['id']);
        $this->assertSame('Fetchable Module', $body['data']['name']);
        $this->assertSame('search', $body['data']['type']);
    }

    public function testGetByIdReturns404ForUnknownId(): void
    {
        $this->apiRequest('GET', '/modules/00000000-0000-0000-0000-000000000000', [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    // ── PATCH /modules/:id ─────────────────────────────────────────────────

    public function testPatchUpdatesModuleConfig(): void
    {
        $this->apiRequest('POST', '/modules', [
            'type' => 'html',
            'name' => 'Original Name',
        ], $this->adminToken);

        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('PATCH', "/modules/{$id}", [
            'name'   => 'Updated Name',
            'config' => ['content' => '<p>Hello</p>'],
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame('Updated Name', $body['data']['name']);
        $this->assertSame(['content' => '<p>Hello</p>'], $body['data']['config']);
        $this->assertNotNull($body['data']['updated_at']);
    }

    public function testPatchReturns404ForUnknownModule(): void
    {
        $this->apiRequest('PATCH', '/modules/00000000-0000-0000-0000-000000000000', [
            'name' => 'Ghost',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testPatchReturns403ForNonAdminUser(): void
    {
        $this->apiRequest('POST', '/modules', [
            'type' => 'menu',
            'name' => 'Locked Module',
        ], $this->adminToken);

        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('PATCH', "/modules/{$id}", ['name' => 'Hacked'], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    // ── DELETE /modules/:id ────────────────────────────────────────────────

    public function testDeleteReturns204(): void
    {
        $this->apiRequest('POST', '/modules', [
            'type' => 'custom',
            'name' => 'To Delete',
        ], $this->adminToken);

        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('DELETE', "/modules/{$id}", [], $this->adminToken);

        $this->assertSame(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteReturns404ForUnknownModule(): void
    {
        $this->apiRequest('DELETE', '/modules/00000000-0000-0000-0000-000000000000', [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteReturns403ForNonAdminUser(): void
    {
        $this->apiRequest('POST', '/modules', [
            'type' => 'html',
            'name' => 'Protected Module',
        ], $this->adminToken);

        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('DELETE', "/modules/{$id}", [], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }
}
