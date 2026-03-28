<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Component\HttpFoundation\Response;

/**
 * Feature tests for:
 *   GET  /portals/{portalId}/pages
 *   POST /portals/{portalId}/pages
 *   POST /pages/{id}/move
 *   POST /pages/{id}/publish
 *   POST /pages/{id}/unpublish
 */
final class PagesTest extends ApiTestCase
{
    private string $adminToken;
    private string $userToken;
    private string $portalId;

    protected function setUp(): void
    {
        parent::setUp();

        $admin            = $this->createUser('admin@example.com', 'adminpass', ['ROLE_ADMIN']);
        $this->adminToken = $this->getToken($admin);

        $regular         = $this->createUser('user@example.com', 'userpass', ['ROLE_USER']);
        $this->userToken = $this->getToken($regular);

        // Create a portal to use as parent for pages
        $this->apiRequest('POST', '/portals', [
            'name'       => 'Test Portal',
            'base_route' => 'test-portal',
        ], $this->adminToken);

        $this->portalId = $this->responseJson()['data']['id'];
    }

    // ── GET /portals/{portalId}/pages ──────────────────────────────────────

    public function testTreeReturnsEmptyDataArray(): void
    {
        $this->apiRequest('GET', "/portals/{$this->portalId}/pages", [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertIsArray($body['data']);
        $this->assertCount(0, $body['data']);
    }

    public function testTreeReturns401WithoutToken(): void
    {
        $this->apiRequest('GET', "/portals/{$this->portalId}/pages");

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testTreeReturnsNestedStructure(): void
    {
        // Create a root page
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title' => 'Root Page',
            'slug'  => 'root',
        ], $this->adminToken);

        $rootId = $this->responseJson()['data']['id'];

        // Create a child page
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title'     => 'Child Page',
            'slug'      => 'child',
            'parent_id' => $rootId,
        ], $this->adminToken);

        $this->apiRequest('GET', "/portals/{$this->portalId}/pages", [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertCount(1, $body['data']);
        $this->assertSame('Root Page', $body['data'][0]['title']);
        $this->assertCount(1, $body['data'][0]['children']);
        $this->assertSame('Child Page', $body['data'][0]['children'][0]['title']);
    }

    // ── POST /portals/{portalId}/pages ─────────────────────────────────────

    public function testCreateReturns201WithNewPage(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title' => 'About Us',
            'slug'  => 'about-us',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertSame('About Us', $body['data']['title']);
        $this->assertSame('about-us', $body['data']['slug']);
        $this->assertSame('/about-us', $body['data']['full_path']);
        $this->assertSame('draft', $body['data']['status']);
        $this->assertSame($this->portalId, $body['data']['portal_id']);
        $this->assertNull($body['data']['parent_id']);
        $this->assertArrayHasKey('id', $body['data']);
        $this->assertArrayHasKey('created_at', $body['data']);
        $this->assertNull($body['data']['updated_at']);
    }

    public function testCreateBuildsFullPathFromParent(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title' => 'Services',
            'slug'  => 'services',
        ], $this->adminToken);

        $parentId = $this->responseJson()['data']['id'];

        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title'     => 'Web Design',
            'slug'      => 'web-design',
            'parent_id' => $parentId,
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame('/services/web-design', $body['data']['full_path']);
        $this->assertSame($parentId, $body['data']['parent_id']);
    }

    public function testCreateReturns422OnMissingTitle(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'slug' => 'no-title',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns422OnMissingSlug(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title' => 'No Slug Page',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns422OnInvalidSlug(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title' => 'Bad Slug Page',
            'slug'  => 'Invalid Slug With Spaces!',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns404WhenParentNotFound(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title'     => 'Orphan Page',
            'slug'      => 'orphan',
            'parent_id' => '00000000-0000-0000-0000-000000000000',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns403ForNonAdminUser(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title' => 'Forbidden Page',
            'slug'  => 'forbidden',
        ], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    // ── POST /pages/{id}/move ──────────────────────────────────────────────

    public function testMoveReturns200AndUpdatesFullPath(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title' => 'Parent Page',
            'slug'  => 'parent',
        ], $this->adminToken);
        $parentId = $this->responseJson()['data']['id'];

        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title' => 'Movable Page',
            'slug'  => 'movable',
        ], $this->adminToken);
        $pageId = $this->responseJson()['data']['id'];

        $this->apiRequest('POST', "/pages/{$pageId}/move", [
            'parent_id' => $parentId,
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame('/parent/movable', $body['data']['full_path']);
        $this->assertSame($parentId, $body['data']['parent_id']);
    }

    public function testMoveToRootClearsParent(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title' => 'Root Anchor',
            'slug'  => 'root-anchor',
        ], $this->adminToken);
        $parentId = $this->responseJson()['data']['id'];

        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title'     => 'Child To Promote',
            'slug'      => 'child-to-promote',
            'parent_id' => $parentId,
        ], $this->adminToken);
        $pageId = $this->responseJson()['data']['id'];

        $this->apiRequest('POST', "/pages/{$pageId}/move", [
            'parent_id' => null,
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertNull($body['data']['parent_id']);
        $this->assertSame('/child-to-promote', $body['data']['full_path']);
    }

    public function testMoveReturns404ForUnknownPage(): void
    {
        $this->apiRequest('POST', '/pages/00000000-0000-0000-0000-000000000000/move', [
            'parent_id' => null,
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testMoveReturns403ForNonAdminUser(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title' => 'Guarded Page',
            'slug'  => 'guarded',
        ], $this->adminToken);
        $pageId = $this->responseJson()['data']['id'];

        $this->apiRequest('POST', "/pages/{$pageId}/move", [
            'parent_id' => null,
        ], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    // ── POST /pages/{id}/publish ───────────────────────────────────────────

    public function testPublishReturns200WithStatusPublished(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title' => 'Draft Page',
            'slug'  => 'draft-page',
        ], $this->adminToken);
        $pageId = $this->responseJson()['data']['id'];

        $this->apiRequest('POST', "/pages/{$pageId}/publish", [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame('published', $body['data']['status']);
        $this->assertNotNull($body['data']['updated_at']);
    }

    public function testPublishReturns404ForUnknownPage(): void
    {
        $this->apiRequest('POST', '/pages/00000000-0000-0000-0000-000000000000/publish', [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testPublishReturns403ForNonAdminUser(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title' => 'Locked Page',
            'slug'  => 'locked-page',
        ], $this->adminToken);
        $pageId = $this->responseJson()['data']['id'];

        $this->apiRequest('POST', "/pages/{$pageId}/publish", [], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    // ── POST /pages/{id}/unpublish ─────────────────────────────────────────

    public function testUnpublishReturns200WithStatusDraft(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title' => 'Page To Unpublish',
            'slug'  => 'page-to-unpublish',
        ], $this->adminToken);
        $pageId = $this->responseJson()['data']['id'];

        // First publish it
        $this->apiRequest('POST', "/pages/{$pageId}/publish", [], $this->adminToken);
        $this->assertSame('published', $this->responseJson()['data']['status']);

        // Then unpublish
        $this->apiRequest('POST', "/pages/{$pageId}/unpublish", [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame('draft', $body['data']['status']);
        $this->assertNotNull($body['data']['updated_at']);
    }

    public function testUnpublishReturns404ForUnknownPage(): void
    {
        $this->apiRequest('POST', '/pages/00000000-0000-0000-0000-000000000000/unpublish', [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testUnpublishReturns403ForNonAdminUser(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title' => 'Protected Published',
            'slug'  => 'protected-published',
        ], $this->adminToken);
        $pageId = $this->responseJson()['data']['id'];

        $this->apiRequest('POST', "/pages/{$pageId}/publish", [], $this->adminToken);

        $this->apiRequest('POST', "/pages/{$pageId}/unpublish", [], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }
}
