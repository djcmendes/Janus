<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Component\HttpFoundation\Response;

/**
 * Feature tests for:
 *   PATCH /pages/{id}/css
 *   PATCH /pages/{id}/acl
 */
final class PageCssAclTest extends ApiTestCase
{
    private string $adminToken;
    private string $userToken;
    private string $portalId;
    private string $pageId;

    protected function setUp(): void
    {
        parent::setUp();

        $admin            = $this->createUser('admin@example.com', 'adminpass', ['ROLE_ADMIN']);
        $this->adminToken = $this->getToken($admin);

        $regular         = $this->createUser('user@example.com', 'userpass', ['ROLE_USER']);
        $this->userToken = $this->getToken($regular);

        $this->apiRequest('POST', '/portals', [
            'name'       => 'CSS Portal',
            'base_route' => 'css-portal',
        ], $this->adminToken);
        $this->portalId = $this->responseJson()['data']['id'];

        $this->apiRequest('POST', "/portals/{$this->portalId}/pages", [
            'title' => 'Home',
            'slug'  => 'home',
        ], $this->adminToken);
        $this->pageId = $this->responseJson()['data']['id'];
    }

    // ── PATCH /pages/{id}/css ──────────────────────────────────────────────

    public function testSetCssReturns200WithUpdatedCss(): void
    {
        $this->apiRequest('PATCH', "/pages/{$this->pageId}/css", [
            'css' => 'body { color: red; }',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertSame('body { color: red; }', $body['data']['custom_css']);
        $this->assertNotNull($body['data']['updated_at']);
    }

    public function testSetCssCanClearCssWithNull(): void
    {
        $this->apiRequest('PATCH', "/pages/{$this->pageId}/css", [
            'css' => 'body { color: red; }',
        ], $this->adminToken);

        $this->apiRequest('PATCH', "/pages/{$this->pageId}/css", [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertNull($this->responseJson()['data']['custom_css']);
    }

    public function testSetCssReturns404ForUnknownPage(): void
    {
        $this->apiRequest('PATCH', '/pages/00000000-0000-0000-0000-000000000000/css', [
            'css' => 'body {}',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testSetCssReturns403ForNonAdmin(): void
    {
        $this->apiRequest('PATCH', "/pages/{$this->pageId}/css", [
            'css' => 'body {}',
        ], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testSetCssReturns401WithoutToken(): void
    {
        $this->apiRequest('PATCH', "/pages/{$this->pageId}/css", ['css' => 'body {}']);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    // ── PATCH /pages/{id}/acl ──────────────────────────────────────────────

    public function testSetAclReturns200WithRules(): void
    {
        $this->apiRequest('PATCH', "/pages/{$this->pageId}/acl", [
            'rules' => [
                ['role_id' => 'a0000000-0000-0000-0000-000000000001', 'permission' => 'view'],
                ['role_id' => 'a0000000-0000-0000-0000-000000000002', 'permission' => 'edit'],
            ],
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertCount(2, $body['data']);
        $this->assertSame('view', $body['data'][0]['permission']);
        $this->assertSame('edit', $body['data'][1]['permission']);
        $this->assertSame('page', $body['data'][0]['subject_type']);
        $this->assertSame($this->pageId, $body['data'][0]['subject_id']);
    }

    public function testSetAclReplacesExistingRules(): void
    {
        $this->apiRequest('PATCH', "/pages/{$this->pageId}/acl", [
            'rules' => [
                ['role_id' => 'a0000000-0000-0000-0000-000000000001', 'permission' => 'view'],
                ['role_id' => 'a0000000-0000-0000-0000-000000000002', 'permission' => 'view'],
            ],
        ], $this->adminToken);

        // Replace with a single rule
        $this->apiRequest('PATCH', "/pages/{$this->pageId}/acl", [
            'rules' => [
                ['role_id' => 'a0000000-0000-0000-0000-000000000003', 'permission' => 'edit'],
            ],
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertCount(1, $body['data']);
        $this->assertSame('a0000000-0000-0000-0000-000000000003', $body['data'][0]['role_id']);
    }

    public function testSetAclWithEmptyRulesClearsAll(): void
    {
        $this->apiRequest('PATCH', "/pages/{$this->pageId}/acl", [
            'rules' => [
                ['role_id' => 'a0000000-0000-0000-0000-000000000001', 'permission' => 'view'],
            ],
        ], $this->adminToken);

        $this->apiRequest('PATCH', "/pages/{$this->pageId}/acl", [
            'rules' => [],
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertCount(0, $this->responseJson()['data']);
    }

    public function testSetAclReturns422WhenRulesIsNotAnArray(): void
    {
        $this->apiRequest('PATCH', "/pages/{$this->pageId}/acl", [
            'rules' => 'not-an-array',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testSetAclReturns404ForUnknownPage(): void
    {
        $this->apiRequest('PATCH', '/pages/00000000-0000-0000-0000-000000000000/acl', [
            'rules' => [],
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testSetAclReturns403ForNonAdmin(): void
    {
        $this->apiRequest('PATCH', "/pages/{$this->pageId}/acl", [
            'rules' => [],
        ], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testSetAclReturns401WithoutToken(): void
    {
        $this->apiRequest('PATCH', "/pages/{$this->pageId}/acl", ['rules' => []]);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }
}
