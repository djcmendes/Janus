<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Component\HttpFoundation\Response;

/**
 * Feature tests for:
 *   GET /portals/{id}/dashboard
 */
final class PortalDashboardTest extends ApiTestCase
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

        $this->apiRequest('POST', '/portals', [
            'name'       => 'Dashboard Portal',
            'base_route' => 'dash-portal',
        ], $this->adminToken);
        $this->portalId = $this->responseJson()['data']['id'];
    }

    // ── GET /portals/{id}/dashboard ────────────────────────────────────────

    public function testDashboardReturnsMetricsShape(): void
    {
        $this->apiRequest('GET', "/portals/{$this->portalId}/dashboard", [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $data = $body['data'];

        $this->assertArrayHasKey('portal_id',       $data);
        $this->assertArrayHasKey('total_pages',     $data);
        $this->assertArrayHasKey('published_pages', $data);
        $this->assertArrayHasKey('draft_pages',     $data);
        $this->assertArrayHasKey('active_magnets',  $data);
        $this->assertArrayHasKey('last_magnet_run', $data);
        $this->assertArrayHasKey('recent_activity', $data);
    }

    public function testDashboardReturnsZeroCountsForEmptyPortal(): void
    {
        $this->apiRequest('GET', "/portals/{$this->portalId}/dashboard", [], $this->adminToken);

        $data = $this->responseJson()['data'];

        $this->assertSame($this->portalId, $data['portal_id']);
        $this->assertSame(0, $data['total_pages']);
        $this->assertSame(0, $data['published_pages']);
        $this->assertSame(0, $data['draft_pages']);
        $this->assertSame(0, $data['active_magnets']);
        $this->assertNull($data['last_magnet_run']);
        $this->assertIsArray($data['recent_activity']);
    }

    public function testDashboardCountsReflectCreatedPages(): void
    {
        // Create two pages
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title' => 'Page One', 'slug' => 'page-one',
        ], $this->adminToken);
        $pageOneId = $this->responseJson()['data']['id'];

        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title' => 'Page Two', 'slug' => 'page-two',
        ], $this->adminToken);

        // Publish first page
        $this->apiRequest('POST', "/pages/{$pageOneId}/publish", [], $this->adminToken);

        $this->apiRequest('GET', "/portals/{$this->portalId}/dashboard", [], $this->adminToken);

        $data = $this->responseJson()['data'];
        $this->assertSame(2, $data['total_pages']);
        $this->assertSame(1, $data['published_pages']);
        $this->assertSame(1, $data['draft_pages']);
    }

    public function testDashboardReturns404ForUnknownPortal(): void
    {
        $this->apiRequest('GET', '/portals/00000000-0000-0000-0000-000000000000/dashboard', [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testDashboardReturns401WithoutToken(): void
    {
        $this->apiRequest('GET', "/portals/{$this->portalId}/dashboard");

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testDashboardIsAccessibleByNonAdminUser(): void
    {
        $this->apiRequest('GET', "/portals/{$this->portalId}/dashboard", [], $this->userToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testRecentActivityIsEmptyArrayInitially(): void
    {
        $this->apiRequest('GET', "/portals/{$this->portalId}/dashboard", [], $this->adminToken);

        $data = $this->responseJson()['data'];
        $this->assertIsArray($data['recent_activity']);
        $this->assertCount(0, $data['recent_activity']);
    }
}
