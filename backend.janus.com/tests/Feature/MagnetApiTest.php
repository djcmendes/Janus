<?php
declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Component\HttpFoundation\Response;

/**
 * Feature tests for:
 *   GET    /portals/{portalId}/magnets
 *   POST   /portals/{portalId}/magnets
 *   PATCH  /portals/{portalId}/magnets/{id}
 *   POST   /portals/{portalId}/magnets/{id}/pause
 *   POST   /portals/{portalId}/magnets/{id}/trigger
 *   GET    /portals/{portalId}/magnets/{id}/runs
 *   DELETE /portals/{portalId}/magnets/{id}
 *   POST   /portals/magnets/{id}/webhook
 */
final class MagnetApiTest extends ApiTestCase
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

        // Create a portal to attach magnets to
        $this->apiRequest('POST', '/portals', [
            'name'       => 'Test Portal',
            'base_route' => 'test-portal',
        ], $this->adminToken);

        $body           = $this->responseJson();
        $this->portalId = $body['data']['id'];
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function createMagnet(array $overrides = []): array
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/magnets", array_merge([
            'name'                 => 'My Magnet',
            'source_type'         => 'api',
            'target_collection_id' => 'articles',
        ], $overrides), $this->adminToken);

        return $this->responseJson()['data'];
    }

    // ── GET /portals/{portalId}/magnets ───────────────────────────────────

    public function testListReturnsEmptyCollection(): void
    {
        $this->apiRequest('GET', "/portals/{$this->portalId}/magnets", [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('meta', $body);
        $this->assertSame(0, $body['meta']['total_count']);
    }

    public function testListReturns401WithoutToken(): void
    {
        $this->apiRequest('GET', "/portals/{$this->portalId}/magnets");

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testListRespectsPagination(): void
    {
        $this->createMagnet(['name' => 'Magnet A']);
        $this->createMagnet(['name' => 'Magnet B']);

        $this->apiRequest('GET', "/portals/{$this->portalId}/magnets?limit=1&offset=0", [], $this->adminToken);

        $body = $this->responseJson();
        $this->assertCount(1, $body['data']);
        $this->assertSame(2, $body['meta']['total_count']);
        $this->assertSame(1, $body['meta']['filter_count']);
    }

    // ── POST /portals/{portalId}/magnets ──────────────────────────────────

    public function testCreateReturns201WithNewMagnet(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/magnets", [
            'name'                 => 'News Magnet',
            'source_type'         => 'rss',
            'target_collection_id' => 'news',
            'schedule'             => '0 * * * *',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $data = $this->responseJson()['data'];
        $this->assertSame('News Magnet', $data['name']);
        $this->assertSame('rss', $data['source_type']);
        $this->assertSame('news', $data['target_collection_id']);
        $this->assertSame('0 * * * *', $data['schedule']);
        $this->assertSame('active', $data['status']);
        $this->assertSame($this->portalId, $data['portal_id']);
        $this->assertNotEmpty($data['id']);
    }

    public function testCreateReturns403ForNonAdmin(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/magnets", [
            'name'                 => 'News Magnet',
            'source_type'         => 'rss',
            'target_collection_id' => 'news',
        ], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns422WhenNameMissing(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/magnets", [
            'source_type'         => 'rss',
            'target_collection_id' => 'news',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns404ForUnknownPortal(): void
    {
        $this->apiRequest('POST', '/portals/00000000-0000-0000-0000-000000000000/magnets', [
            'name'                 => 'X',
            'source_type'         => 'api',
            'target_collection_id' => 'items',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    // ── PATCH /portals/{portalId}/magnets/{id} ────────────────────────────

    public function testUpdateRenamesMagnet(): void
    {
        $magnet = $this->createMagnet();

        $this->apiRequest('PATCH', "/portals/{$this->portalId}/magnets/{$magnet['id']}", [
            'name' => 'Renamed Magnet',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $data = $this->responseJson()['data'];
        $this->assertSame('Renamed Magnet', $data['name']);
    }

    public function testUpdateChangesSourceConfig(): void
    {
        $magnet = $this->createMagnet(['source_type' => 'api']);

        $this->apiRequest('PATCH', "/portals/{$this->portalId}/magnets/{$magnet['id']}", [
            'source_type'   => 'api',
            'source_config' => ['url' => 'https://api.example.com/items', 'method' => 'GET'],
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $data = $this->responseJson()['data'];
        $this->assertSame('api', $data['source_type']);
        $this->assertSame('https://api.example.com/items', $data['source_config']['url']);
    }

    public function testUpdateReturns404ForUnknownMagnet(): void
    {
        $this->apiRequest('PATCH', "/portals/{$this->portalId}/magnets/00000000-0000-0000-0000-000000000000", [
            'name' => 'X',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    // ── POST /portals/{portalId}/magnets/{id}/pause ───────────────────────

    public function testPauseTogglesPauseAndResume(): void
    {
        $magnet = $this->createMagnet();
        $this->assertSame('active', $magnet['status']);

        // Pause
        $this->apiRequest('POST', "/portals/{$this->portalId}/magnets/{$magnet['id']}/pause", [], $this->adminToken);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('paused', $this->responseJson()['data']['status']);

        // Resume
        $this->apiRequest('POST', "/portals/{$this->portalId}/magnets/{$magnet['id']}/pause", [], $this->adminToken);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('active', $this->responseJson()['data']['status']);
    }

    // ── POST /portals/{portalId}/magnets/{id}/trigger ─────────────────────

    public function testTriggerReturns202WithRunId(): void
    {
        $magnet = $this->createMagnet();

        $this->apiRequest('POST', "/portals/{$this->portalId}/magnets/{$magnet['id']}/trigger", [], $this->adminToken);

        $this->assertSame(Response::HTTP_ACCEPTED, $this->client->getResponse()->getStatusCode());

        $data = $this->responseJson()['data'];
        $this->assertNotEmpty($data['id']);
        $this->assertSame($magnet['id'], $data['magnet_id']);
        $this->assertNull($data['finished_at']);
    }

    public function testTriggerReturns404ForUnknownMagnet(): void
    {
        $this->apiRequest('POST', "/portals/{$this->portalId}/magnets/00000000-0000-0000-0000-000000000000/trigger", [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    // ── GET /portals/{portalId}/magnets/{id}/runs ─────────────────────────

    public function testRunHistoryReturnsEmptyInitially(): void
    {
        $magnet = $this->createMagnet();

        $this->apiRequest('GET', "/portals/{$this->portalId}/magnets/{$magnet['id']}/runs", [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame(0, $body['meta']['total_count']);
        $this->assertCount(0, $body['data']);
    }

    public function testRunHistoryRecordsAfterTrigger(): void
    {
        $magnet = $this->createMagnet();

        $this->apiRequest('POST', "/portals/{$this->portalId}/magnets/{$magnet['id']}/trigger", [], $this->adminToken);

        $this->apiRequest('GET', "/portals/{$this->portalId}/magnets/{$magnet['id']}/runs", [], $this->adminToken);

        $body = $this->responseJson();
        $this->assertSame(1, $body['meta']['total_count']);
        $this->assertCount(1, $body['data']);
        $this->assertSame($magnet['id'], $body['data'][0]['magnet_id']);
    }

    public function testRunHistoryReturns404ForUnknownMagnet(): void
    {
        $this->apiRequest('GET', "/portals/{$this->portalId}/magnets/00000000-0000-0000-0000-000000000000/runs", [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    // ── DELETE /portals/{portalId}/magnets/{id} ───────────────────────────

    public function testDeleteReturns204(): void
    {
        $magnet = $this->createMagnet();

        $this->apiRequest('DELETE', "/portals/{$this->portalId}/magnets/{$magnet['id']}", [], $this->adminToken);

        $this->assertSame(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteReturns404WhenAlreadyDeleted(): void
    {
        $magnet = $this->createMagnet();

        $this->apiRequest('DELETE', "/portals/{$this->portalId}/magnets/{$magnet['id']}", [], $this->adminToken);
        $this->apiRequest('DELETE', "/portals/{$this->portalId}/magnets/{$magnet['id']}", [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteReturns403ForNonAdmin(): void
    {
        $magnet = $this->createMagnet();

        $this->apiRequest('DELETE', "/portals/{$this->portalId}/magnets/{$magnet['id']}", [], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    // ── POST /portals/magnets/{id}/webhook ────────────────────────────────

    public function testWebhookReturns202WithValidSecret(): void
    {
        $magnet = $this->createMagnet(['source_type' => 'webhook']);

        // Set the webhook secret via PATCH
        $this->apiRequest('PATCH', "/portals/{$this->portalId}/magnets/{$magnet['id']}", [
            'source_type'   => 'webhook',
            'source_config' => ['secret' => 'my-super-secret', 'field_map' => ['title' => 'name']],
        ], $this->adminToken);

        // Call webhook endpoint
        $this->client->request(
            'POST',
            "/portals/magnets/{$magnet['id']}/webhook",
            [],
            [],
            [
                'CONTENT_TYPE'       => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer my-super-secret',
                'HTTP_X_CLIENT_TYPE' => 'web',
            ],
            json_encode(['title' => 'Breaking News'], JSON_THROW_ON_ERROR)
        );

        $this->assertSame(Response::HTTP_ACCEPTED, $this->client->getResponse()->getStatusCode());

        $data = $this->responseJson()['data'];
        $this->assertNotEmpty($data['run_id']);
        $this->assertSame('queued', $data['status']);
    }

    public function testWebhookReturns401WithWrongSecret(): void
    {
        $magnet = $this->createMagnet(['source_type' => 'webhook']);

        $this->apiRequest('PATCH', "/portals/{$this->portalId}/magnets/{$magnet['id']}", [
            'source_type'   => 'webhook',
            'source_config' => ['secret' => 'correct-secret'],
        ], $this->adminToken);

        $this->client->request(
            'POST',
            "/portals/magnets/{$magnet['id']}/webhook",
            [],
            [],
            [
                'CONTENT_TYPE'       => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer wrong-secret',
                'HTTP_X_CLIENT_TYPE' => 'web',
            ],
            '{}'
        );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }
}
