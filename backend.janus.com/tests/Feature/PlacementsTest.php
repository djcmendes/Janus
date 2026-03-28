<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Component\HttpFoundation\Response;

/**
 * Feature tests for:
 *   POST   /pages/{pageId}/placements
 *   GET    /pages/{pageId}/placements
 *   DELETE /pages/{pageId}/placements/{id}
 *   POST   /pages/{pageId}/placements/reorder
 */
final class PlacementsTest extends ApiTestCase
{
    private string $adminToken;
    private string $userToken;
    private string $fakePageId;
    private string $fakeModuleId;

    protected function setUp(): void
    {
        parent::setUp();

        $admin            = $this->createUser('admin@example.com', 'adminpass', ['ROLE_ADMIN']);
        $this->adminToken = $this->getToken($admin);

        $regular         = $this->createUser('user@example.com', 'userpass', ['ROLE_USER']);
        $this->userToken = $this->getToken($regular);

        // Use stable fake UUIDs as foreign keys (no FK constraints in SQLite test DB)
        $this->fakePageId   = '01900000-0000-7000-8000-000000000001';
        $this->fakeModuleId = '01900000-0000-7000-8000-000000000002';
    }

    // ── POST /pages/{pageId}/placements ────────────────────────────────────

    public function testCreatePlacementReturns201(): void
    {
        $this->apiRequest('POST', "/pages/{$this->fakePageId}/placements", [
            'module_id'     => $this->fakeModuleId,
            'position_name' => 'header',
            'sort_order'    => 0,
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertSame($this->fakePageId, $body['data']['page_id']);
        $this->assertSame($this->fakeModuleId, $body['data']['module_id']);
        $this->assertSame('header', $body['data']['position_name']);
        $this->assertSame(0, $body['data']['sort_order']);
        $this->assertArrayHasKey('id', $body['data']);
    }

    public function testCreatePlacementReturns422OnMissingModuleId(): void
    {
        $this->apiRequest('POST', "/pages/{$this->fakePageId}/placements", [
            'position_name' => 'header',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testCreatePlacementReturns422OnMissingPositionName(): void
    {
        $this->apiRequest('POST', "/pages/{$this->fakePageId}/placements", [
            'module_id' => $this->fakeModuleId,
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testCreatePlacementReturns401WithoutToken(): void
    {
        $this->apiRequest('POST', "/pages/{$this->fakePageId}/placements", [
            'module_id'     => $this->fakeModuleId,
            'position_name' => 'header',
        ]);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testCreatePlacementReturns403ForNonAdmin(): void
    {
        $this->apiRequest('POST', "/pages/{$this->fakePageId}/placements", [
            'module_id'     => $this->fakeModuleId,
            'position_name' => 'header',
        ], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    // ── GET /pages/{pageId}/placements ─────────────────────────────────────

    public function testListPlacementsReturnsEmptyArray(): void
    {
        $this->apiRequest('GET', "/pages/{$this->fakePageId}/placements", [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertIsArray($body['data']);
        $this->assertCount(0, $body['data']);
    }

    public function testListPlacementsReturnsCreatedPlacements(): void
    {
        $this->apiRequest('POST', "/pages/{$this->fakePageId}/placements", [
            'module_id'     => $this->fakeModuleId,
            'position_name' => 'sidebar',
            'sort_order'    => 1,
        ], $this->adminToken);

        $this->apiRequest('POST', "/pages/{$this->fakePageId}/placements", [
            'module_id'     => $this->fakeModuleId,
            'position_name' => 'sidebar',
            'sort_order'    => 2,
        ], $this->adminToken);

        $this->apiRequest('GET', "/pages/{$this->fakePageId}/placements", [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertCount(2, $body['data']);
    }

    public function testListPlacementsReturns401WithoutToken(): void
    {
        $this->apiRequest('GET', "/pages/{$this->fakePageId}/placements");

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    // ── DELETE /pages/{pageId}/placements/{id} ─────────────────────────────

    public function testDeletePlacementReturns204(): void
    {
        $this->apiRequest('POST', "/pages/{$this->fakePageId}/placements", [
            'module_id'     => $this->fakeModuleId,
            'position_name' => 'footer',
        ], $this->adminToken);

        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('DELETE', "/pages/{$this->fakePageId}/placements/{$id}", [], $this->adminToken);

        $this->assertSame(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    public function testDeletePlacementReturns404ForUnknownId(): void
    {
        $this->apiRequest('DELETE', "/pages/{$this->fakePageId}/placements/00000000-0000-0000-0000-000000000000", [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testDeletePlacementReturns403ForNonAdmin(): void
    {
        $this->apiRequest('POST', "/pages/{$this->fakePageId}/placements", [
            'module_id'     => $this->fakeModuleId,
            'position_name' => 'footer',
        ], $this->adminToken);

        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('DELETE', "/pages/{$this->fakePageId}/placements/{$id}", [], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    // ── POST /pages/{pageId}/placements/reorder ────────────────────────────

    public function testReorderPlacementsReturns200(): void
    {
        $this->apiRequest('POST', "/pages/{$this->fakePageId}/placements", [
            'module_id'     => $this->fakeModuleId,
            'position_name' => 'main',
            'sort_order'    => 1,
        ], $this->adminToken);
        $id1 = $this->responseJson()['data']['id'];

        $this->apiRequest('POST', "/pages/{$this->fakePageId}/placements", [
            'module_id'     => $this->fakeModuleId,
            'position_name' => 'main',
            'sort_order'    => 2,
        ], $this->adminToken);
        $id2 = $this->responseJson()['data']['id'];

        $this->apiRequest('POST', "/pages/{$this->fakePageId}/placements/reorder", [
            'items' => [
                ['id' => $id1, 'sort_order' => 10],
                ['id' => $id2, 'sort_order' => 20],
            ],
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
    }

    public function testReorderPlacementsReturns404ForUnknownPlacement(): void
    {
        $this->apiRequest('POST', "/pages/{$this->fakePageId}/placements/reorder", [
            'items' => [
                ['id' => '00000000-0000-0000-0000-000000000000', 'sort_order' => 1],
            ],
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testReorderPlacementsReturns401WithoutToken(): void
    {
        $this->apiRequest('POST', "/pages/{$this->fakePageId}/placements/reorder", [
            'items' => [],
        ]);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testReorderPlacementsReturns403ForNonAdmin(): void
    {
        $this->apiRequest('POST', "/pages/{$this->fakePageId}/placements/reorder", [
            'items' => [],
        ], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }
}
