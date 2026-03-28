<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Component\HttpFoundation\Response;

/**
 * Feature tests for:
 *   GET    /components
 *   GET    /components/{id}
 *   POST   /components
 *   PATCH  /components/{id}
 *   DELETE /components/{id}
 *   GET    /pages/{id}/layout
 */
final class ComponentsTest extends ApiTestCase
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

    // ── GET /components ────────────────────────────────────────────────────

    public function testListReturnsEmptyDataWithMeta(): void
    {
        $this->apiRequest('GET', '/components', [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('meta', $body);
        $this->assertSame(0, $body['meta']['total_count']);
        $this->assertSame(0, $body['meta']['filter_count']);
    }

    public function testListReturns401WithoutToken(): void
    {
        $this->apiRequest('GET', '/components');

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testListRespectsPaginationParams(): void
    {
        $this->apiRequest('POST', '/components', ['type' => 'content'], $this->adminToken);
        $this->apiRequest('POST', '/components', ['type' => 'form'], $this->adminToken);

        $this->apiRequest('GET', '/components?limit=1&offset=0', [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertCount(1, $body['data']);
        $this->assertSame(2, $body['meta']['total_count']);
        $this->assertSame(1, $body['meta']['filter_count']);
    }

    // ── POST /components ───────────────────────────────────────────────────

    public function testCreateReturns201WithNewComponent(): void
    {
        $this->apiRequest('POST', '/components', [
            'type'          => 'content',
            'collection_id' => 'articles',
            'query_config'  => ['filter' => ['status' => 'published']],
            'render_config' => ['template' => 'card'],
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('id', $body['data']);
        $this->assertSame('content', $body['data']['type']);
        $this->assertSame('articles', $body['data']['collection_id']);
        $this->assertSame(['filter' => ['status' => 'published']], $body['data']['query_config']);
        $this->assertSame(['template' => 'card'], $body['data']['render_config']);
        $this->assertArrayHasKey('created_at', $body['data']);
    }

    public function testCreateReturns201WithMinimalData(): void
    {
        $this->apiRequest('POST', '/components', [
            'type' => 'redirect',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame('redirect', $body['data']['type']);
        $this->assertNull($body['data']['collection_id']);
    }

    public function testCreateReturns422OnMissingType(): void
    {
        $this->apiRequest('POST', '/components', [
            'collection_id' => 'articles',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns422OnInvalidType(): void
    {
        $this->apiRequest('POST', '/components', [
            'type' => 'invalid-type',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns401WithoutToken(): void
    {
        $this->apiRequest('POST', '/components', ['type' => 'content']);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns403ForNonAdmin(): void
    {
        $this->apiRequest('POST', '/components', ['type' => 'content'], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    // ── GET /components/{id} ───────────────────────────────────────────────

    public function testGetByIdReturns200WithComponent(): void
    {
        $this->apiRequest('POST', '/components', [
            'type'          => 'collection-list',
            'collection_id' => 'news',
        ], $this->adminToken);

        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('GET', "/components/{$id}", [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame($id, $body['data']['id']);
        $this->assertSame('collection-list', $body['data']['type']);
        $this->assertSame('news', $body['data']['collection_id']);
    }

    public function testGetByIdReturns404ForUnknownId(): void
    {
        $this->apiRequest('GET', '/components/00000000-0000-0000-0000-000000000000', [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('errors', $body);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    public function testGetByIdReturns401WithoutToken(): void
    {
        $this->apiRequest('GET', '/components/00000000-0000-0000-0000-000000000000');

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    // ── PATCH /components/{id} ─────────────────────────────────────────────

    public function testPatchReturns200WithUpdatedComponent(): void
    {
        $this->apiRequest('POST', '/components', [
            'type' => 'form',
        ], $this->adminToken);

        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('PATCH', "/components/{$id}", [
            'collection_id' => 'contact_submissions',
            'query_config'  => ['fields' => ['name', 'email']],
            'render_config' => ['layout' => 'vertical'],
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame($id, $body['data']['id']);
        $this->assertSame('contact_submissions', $body['data']['collection_id']);
        $this->assertSame(['fields' => ['name', 'email']], $body['data']['query_config']);
        $this->assertSame(['layout' => 'vertical'], $body['data']['render_config']);
        $this->assertNotNull($body['data']['updated_at']);
    }

    public function testPatchReturns404ForUnknownId(): void
    {
        $this->apiRequest('PATCH', '/components/00000000-0000-0000-0000-000000000000', [
            'collection_id' => 'articles',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testPatchReturns401WithoutToken(): void
    {
        $this->apiRequest('PATCH', '/components/00000000-0000-0000-0000-000000000000', [
            'collection_id' => 'articles',
        ]);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testPatchReturns403ForNonAdmin(): void
    {
        $this->apiRequest('POST', '/components', ['type' => 'content'], $this->adminToken);
        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('PATCH', "/components/{$id}", ['collection_id' => 'x'], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    // ── DELETE /components/{id} ────────────────────────────────────────────

    public function testDeleteReturns204(): void
    {
        $this->apiRequest('POST', '/components', ['type' => 'content'], $this->adminToken);
        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('DELETE', "/components/{$id}", [], $this->adminToken);

        $this->assertSame(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    public function testDeletedComponentIsNotFoundAfterDeletion(): void
    {
        $this->apiRequest('POST', '/components', ['type' => 'content'], $this->adminToken);
        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('DELETE', "/components/{$id}", [], $this->adminToken);
        $this->apiRequest('GET', "/components/{$id}", [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteReturns404ForUnknownId(): void
    {
        $this->apiRequest('DELETE', '/components/00000000-0000-0000-0000-000000000000', [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteReturns401WithoutToken(): void
    {
        $this->apiRequest('DELETE', '/components/00000000-0000-0000-0000-000000000000');

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteReturns403ForNonAdmin(): void
    {
        $this->apiRequest('POST', '/components', ['type' => 'content'], $this->adminToken);
        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('DELETE', "/components/{$id}", [], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    // ── GET /pages/{id}/layout ─────────────────────────────────────────────

    public function testGetLayoutReturns404ForUnknownPage(): void
    {
        $this->apiRequest('GET', '/pages/00000000-0000-0000-0000-000000000000/layout', [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('errors', $body);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    public function testGetLayoutReturns401WithoutToken(): void
    {
        $this->apiRequest('GET', '/pages/00000000-0000-0000-0000-000000000000/layout');

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testGetLayoutReturns200WithFullStructure(): void
    {
        // Create a portal first
        $this->apiRequest('POST', '/portals', [
            'name'  => 'Test Portal',
            'route' => 'test-portal',
        ], $this->adminToken);
        $portalId = $this->responseJson()['data']['id'];

        // Create a page
        $this->apiRequest('POST', '/pages', [
            'portal_id' => $portalId,
            'title'     => 'Home Page',
            'slug'      => 'home',
        ], $this->adminToken);
        $pageId = $this->responseJson()['data']['id'];

        // Fetch layout
        $this->apiRequest('GET', "/pages/{$pageId}/layout", [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $data = $body['data'];

        $this->assertArrayHasKey('page', $data);
        $this->assertArrayHasKey('layout_template', $data);
        $this->assertArrayHasKey('positions', $data);
        $this->assertArrayHasKey('center_component', $data);

        $this->assertSame($pageId, $data['page']['id']);
        $this->assertNull($data['layout_template']);
        $this->assertIsArray($data['positions']);
        $this->assertNull($data['center_component']);
    }
}
