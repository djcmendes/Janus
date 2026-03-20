<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Component\HttpFoundation\Response;

/**
 * Feature tests for:
 *   GET   /settings
 *   PATCH /settings
 */
final class SettingsTest extends ApiTestCase
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

    // ── GET /settings ──────────────────────────────────────────────────────

    public function testGetReturnsDefaultSettings(): void
    {
        $this->apiRequest('GET', '/settings', [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);

        $data = $body['data'];
        $this->assertArrayHasKey('project_name', $data);
        $this->assertArrayHasKey('default_language', $data);
        $this->assertArrayHasKey('default_appearance', $data);
    }

    public function testGetReturns401WithoutToken(): void
    {
        $this->apiRequest('GET', '/settings');

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testGetIsAccessibleToRegularUsers(): void
    {
        $this->apiRequest('GET', '/settings', [], $this->userToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    // ── PATCH /settings ────────────────────────────────────────────────────

    public function testPatchUpdatesProjectName(): void
    {
        $this->apiRequest('PATCH', '/settings', [
            'project_name' => 'My Custom Project',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame('My Custom Project', $body['data']['project_name']);
    }

    public function testPatchUpdatesMultipleFields(): void
    {
        $this->apiRequest('PATCH', '/settings', [
            'project_name'       => 'Janus CMS',
            'default_language'   => 'pt-BR',
            'default_appearance' => 'dark',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame('Janus CMS', $body['data']['project_name']);
        $this->assertSame('pt-BR', $body['data']['default_language']);
        $this->assertSame('dark', $body['data']['default_appearance']);
    }

    public function testPatchPersistsChangesAcrossRequests(): void
    {
        $this->apiRequest('PATCH', '/settings', [
            'project_name' => 'Persisted Name',
        ], $this->adminToken);

        // Fetch again
        $this->apiRequest('GET', '/settings', [], $this->adminToken);

        $body = $this->responseJson();
        $this->assertSame('Persisted Name', $body['data']['project_name']);
    }

    public function testPatchReturns403ForNonAdminUser(): void
    {
        $this->apiRequest('PATCH', '/settings', [
            'project_name' => 'Hacked',
        ], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testPatchReturns401WithoutToken(): void
    {
        $this->apiRequest('PATCH', '/settings', ['project_name' => 'Anon']);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }
}
