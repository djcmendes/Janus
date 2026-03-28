<?php
declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Component\HttpFoundation\Response;

/**
 * Verifies that full_path values are kept consistent with the page tree.
 *
 * Rules under test:
 *   1. A root page gets full_path = "/<slug>"
 *   2. A child page gets full_path = "<parent_full_path>/<slug>"
 *   3. Two pages in the same portal cannot have the same full_path (409)
 *   4. A page in portal A can have the same full_path as a page in portal B
 */
final class PageFullPathIntegrityTest extends ApiTestCase
{
    private string $adminToken;
    private string $portalId;
    private string $portalBId;

    protected function setUp(): void
    {
        parent::setUp();

        $admin            = $this->createUser('admin@example.com', 'adminpass', ['ROLE_ADMIN']);
        $this->adminToken = $this->getToken($admin);

        $this->apiRequest('POST', '/portals', ['name' => 'Portal A', 'base_route' => 'a'], $this->adminToken);
        $this->portalId = $this->responseJson()['data']['id'];

        $this->apiRequest('POST', '/portals', ['name' => 'Portal B', 'base_route' => 'b'], $this->adminToken);
        $this->portalBId = $this->responseJson()['data']['id'];
    }

    // ── 1. Root page full_path ────────────────────────────────────────────

    public function testRootPageGetsSlashPrefixedPath(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'slug'  => 'home',
            'title' => 'Home',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $page = $this->responseJson()['data'];
        $this->assertSame('/home', $page['full_path']);
    }

    // ── 2. Child page inherits parent path ───────────────────────────────

    public function testChildPageConcatenatesParentFullPath(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'slug'  => 'about',
            'title' => 'About',
        ], $this->adminToken);
        $parent = $this->responseJson()['data'];

        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'slug'      => 'team',
            'title'     => 'Team',
            'parent_id' => $parent['id'],
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $child = $this->responseJson()['data'];
        $this->assertSame('/about/team', $child['full_path']);
    }

    // ── 3. Deeply nested path ────────────────────────────────────────────

    public function testDeeplyNestedPageBuildsFullPath(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", ['slug' => 'products', 'title' => 'Products'], $this->adminToken);
        $l1 = $this->responseJson()['data'];

        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", ['slug' => 'software', 'title' => 'Software', 'parent_id' => $l1['id']], $this->adminToken);
        $l2 = $this->responseJson()['data'];

        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", ['slug' => 'janus', 'title' => 'Janus', 'parent_id' => $l2['id']], $this->adminToken);
        $l3 = $this->responseJson()['data'];

        $this->assertSame('/products/software/janus', $l3['full_path']);
    }

    // ── 4. Duplicate full_path in same portal is rejected ─────────────────

    public function testDuplicateFullPathInSamePortalIsRejected(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", ['slug' => 'contact', 'title' => 'Contact'], $this->adminToken);
        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        // Same slug → same full_path → should be rejected
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", ['slug' => 'contact', 'title' => 'Contact 2'], $this->adminToken);
        $status = $this->client->getResponse()->getStatusCode();

        // Either 409 Conflict or 422 Unprocessable — implementation may vary
        $this->assertContains($status, [Response::HTTP_CONFLICT, Response::HTTP_UNPROCESSABLE_ENTITY]);
    }

    // ── 5. Same full_path is allowed across different portals ────────────

    public function testSameFullPathAllowedInDifferentPortals(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", ['slug' => 'home', 'title' => 'Home A'], $this->adminToken);
        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $this->apiRequest('POST', "/portals/{$this->portalBId}/pages", ['slug' => 'home', 'title' => 'Home B'], $this->adminToken);
        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $pageB = $this->responseJson()['data'];
        $this->assertSame('/home', $pageB['full_path']);
    }

    // ── 6. Move updates full_path ─────────────────────────────────────────

    public function testMovePageUpdatesFullPath(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", ['slug' => 'section', 'title' => 'Section'], $this->adminToken);
        $section = $this->responseJson()['data'];

        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", ['slug' => 'child', 'title' => 'Child'], $this->adminToken);
        $child = $this->responseJson()['data'];

        $this->assertSame('/child', $child['full_path']);

        // Move child under section
        $this->apiRequest('PATCH', "/pages/{$child['id']}/move", [
            'parent_id'  => $section['id'],
            'sort_order' => 0,
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $moved = $this->responseJson()['data'];
        $this->assertSame('/section/child', $moved['full_path']);
    }
}
