<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Component\HttpFoundation\Response;

/**
 * Feature tests for:
 *   GET    /portals
 *   GET    /portals/:id
 *   POST   /portals
 *   PATCH  /portals/:id
 *   DELETE /portals/:id
 */
final class PortalsTest extends ApiTestCase
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

    // ── GET /portals ───────────────────────────────────────────────────────

    public function testListReturnsEmptyDataWithMeta(): void
    {
        $this->apiRequest('GET', '/portals', [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('meta', $body);
        $this->assertSame(0, $body['meta']['total_count']);
        $this->assertSame(0, $body['meta']['filter_count']);
    }

    public function testListReturns401WithoutToken(): void
    {
        $this->apiRequest('GET', '/portals');

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testListRespectsPaginationParams(): void
    {
        // Create two portals
        $this->apiRequest('POST', '/portals', [
            'name'       => 'Portal Alpha',
            'base_route' => 'alpha',
        ], $this->adminToken);

        $this->apiRequest('POST', '/portals', [
            'name'       => 'Portal Beta',
            'base_route' => 'beta',
        ], $this->adminToken);

        $this->apiRequest('GET', '/portals?limit=1&offset=0', [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertCount(1, $body['data']);
        $this->assertSame(2, $body['meta']['total_count']);
        $this->assertSame(1, $body['meta']['filter_count']);
    }

    // ── POST /portals ──────────────────────────────────────────────────────

    public function testCreateReturns201WithNewPortal(): void
    {
        $this->apiRequest('POST', '/portals', [
            'name'       => 'My Portal',
            'base_route' => 'my-portal',
            'status'     => 'draft',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertSame('My Portal', $body['data']['name']);
        $this->assertSame('/my-portal', $body['data']['base_route']);
        $this->assertSame('draft', $body['data']['status']);
        $this->assertArrayHasKey('id', $body['data']);
        $this->assertArrayHasKey('created_at', $body['data']);
        $this->assertNull($body['data']['updated_at']);
    }

    public function testCreateReturns422OnMissingName(): void
    {
        $this->apiRequest('POST', '/portals', [
            'base_route' => 'some-route',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns422OnMissingBaseRoute(): void
    {
        $this->apiRequest('POST', '/portals', [
            'name' => 'No Route Portal',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns409OnDuplicateBaseRoute(): void
    {
        $this->apiRequest('POST', '/portals', [
            'name'       => 'First Portal',
            'base_route' => 'duplicate-route',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $this->apiRequest('POST', '/portals', [
            'name'       => 'Second Portal',
            'base_route' => 'duplicate-route',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_CONFLICT, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns403ForNonAdminUser(): void
    {
        $this->apiRequest('POST', '/portals', [
            'name'       => 'Forbidden Portal',
            'base_route' => 'forbidden',
        ], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    // ── GET /portals/:id ───────────────────────────────────────────────────

    public function testGetByIdReturnsSinglePortal(): void
    {
        $this->apiRequest('POST', '/portals', [
            'name'       => 'Fetchable Portal',
            'base_route' => 'fetchable',
        ], $this->adminToken);

        $created = $this->responseJson();
        $id      = $created['data']['id'];

        $this->apiRequest('GET', "/portals/{$id}", [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame($id, $body['data']['id']);
        $this->assertSame('Fetchable Portal', $body['data']['name']);
    }

    public function testGetByIdReturns404ForUnknownId(): void
    {
        $this->apiRequest('GET', '/portals/00000000-0000-0000-0000-000000000000', [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    // ── PATCH /portals/:id ─────────────────────────────────────────────────

    public function testPatchUpdatesPortal(): void
    {
        $this->apiRequest('POST', '/portals', [
            'name'       => 'Original Name',
            'base_route' => 'original-route',
        ], $this->adminToken);

        $created = $this->responseJson();
        $id      = $created['data']['id'];

        $this->apiRequest('PATCH', "/portals/{$id}", [
            'name'   => 'Updated Name',
            'status' => 'active',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame('Updated Name', $body['data']['name']);
        $this->assertSame('active', $body['data']['status']);
        $this->assertNotNull($body['data']['updated_at']);
    }

    public function testPatchReturns404ForUnknownPortal(): void
    {
        $this->apiRequest('PATCH', '/portals/00000000-0000-0000-0000-000000000000', [
            'name' => 'Ghost',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testPatchReturns403ForNonAdminUser(): void
    {
        $this->apiRequest('POST', '/portals', [
            'name'       => 'Locked Portal',
            'base_route' => 'locked',
        ], $this->adminToken);

        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('PATCH', "/portals/{$id}", ['name' => 'Hacked'], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    // ── DELETE /portals/:id ────────────────────────────────────────────────

    public function testDeleteReturns204AndArchivesPortal(): void
    {
        $this->apiRequest('POST', '/portals', [
            'name'       => 'To Be Archived',
            'base_route' => 'archive-me',
        ], $this->adminToken);

        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('DELETE', "/portals/{$id}", [], $this->adminToken);

        $this->assertSame(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());

        // Confirm it's archived, not deleted
        $this->apiRequest('GET', "/portals/{$id}", [], $this->adminToken);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('archived', $this->responseJson()['data']['status']);
    }

    public function testDeleteReturns404ForUnknownPortal(): void
    {
        $this->apiRequest('DELETE', '/portals/00000000-0000-0000-0000-000000000000', [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteReturns403ForNonAdminUser(): void
    {
        $this->apiRequest('POST', '/portals', [
            'name'       => 'Protected Portal',
            'base_route' => 'protected',
        ], $this->adminToken);

        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('DELETE', "/portals/{$id}", [], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }
}
