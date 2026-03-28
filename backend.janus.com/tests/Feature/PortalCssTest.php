<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Component\HttpFoundation\Response;

/**
 * Feature tests for:
 *   PATCH /portals/{id}/css
 */
final class PortalCssTest extends ApiTestCase
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
            'name'       => 'Styled Portal',
            'base_route' => 'styled-portal',
        ], $this->adminToken);
        $this->portalId = $this->responseJson()['data']['id'];
    }

    // ── PATCH /portals/{id}/css ────────────────────────────────────────────

    public function testSetCssReturns200WithUpdatedCss(): void
    {
        $this->apiRequest('PATCH', "/portals/{$this->portalId}/css", [
            'css' => ':root { --primary: #fff; }',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertSame(':root { --primary: #fff; }', $body['data']['portal_css']);
        $this->assertNotNull($body['data']['updated_at']);
    }

    public function testSetCssCanClearCssWithNull(): void
    {
        $this->apiRequest('PATCH', "/portals/{$this->portalId}/css", [
            'css' => ':root { --primary: #fff; }',
        ], $this->adminToken);

        $this->apiRequest('PATCH', "/portals/{$this->portalId}/css", [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertNull($this->responseJson()['data']['portal_css']);
    }

    public function testSetCssReturns404ForUnknownPortal(): void
    {
        $this->apiRequest('PATCH', '/portals/00000000-0000-0000-0000-000000000000/css', [
            'css' => 'body {}',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testSetCssReturns403ForNonAdmin(): void
    {
        $this->apiRequest('PATCH', "/portals/{$this->portalId}/css", [
            'css' => 'body {}',
        ], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testSetCssReturns401WithoutToken(): void
    {
        $this->apiRequest('PATCH', "/portals/{$this->portalId}/css", ['css' => 'body {}']);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }
}
