<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Component\HttpFoundation\Response;

/**
 * Feature tests for:
 *   GET   /layout-templates
 *   GET   /layout-templates/:id
 *   POST  /layout-templates
 *   PATCH /layout-templates/:id
 */
final class LayoutTemplatesTest extends ApiTestCase
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

    // ── GET /layout-templates ──────────────────────────────────────────────

    public function testListReturnsEmptyDataWithMeta(): void
    {
        $this->apiRequest('GET', '/layout-templates', [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('meta', $body);
        $this->assertSame(0, $body['meta']['total_count']);
        $this->assertSame(0, $body['meta']['filter_count']);
    }

    public function testListReturns401WithoutToken(): void
    {
        $this->apiRequest('GET', '/layout-templates');

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testListRespectsPaginationParams(): void
    {
        $this->apiRequest('POST', '/layout-templates', [
            'name'            => 'Template Alpha',
            'positions'       => [['name' => 'header']],
            'template_markup' => '<header>{{ header }}</header>',
        ], $this->adminToken);

        $this->apiRequest('POST', '/layout-templates', [
            'name'            => 'Template Beta',
            'positions'       => [['name' => 'footer']],
            'template_markup' => '<footer>{{ footer }}</footer>',
        ], $this->adminToken);

        $this->apiRequest('GET', '/layout-templates?limit=1&offset=0', [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertCount(1, $body['data']);
        $this->assertSame(2, $body['meta']['total_count']);
        $this->assertSame(1, $body['meta']['filter_count']);
    }

    // ── POST /layout-templates ─────────────────────────────────────────────

    public function testCreateReturns201WithNewTemplate(): void
    {
        $this->apiRequest('POST', '/layout-templates', [
            'name'            => 'Two-Column Layout',
            'positions'       => [
                ['name' => 'sidebar', 'description' => 'Left sidebar'],
                ['name' => 'main',    'description' => 'Main content area'],
            ],
            'template_markup' => '<aside>{{ sidebar }}</aside><main>{{ main }}</main>',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertSame('Two-Column Layout', $body['data']['name']);
        $this->assertCount(2, $body['data']['positions']);
        $this->assertSame('sidebar', $body['data']['positions'][0]['name']);
        $this->assertArrayHasKey('id', $body['data']);
        $this->assertArrayHasKey('created_at', $body['data']);
        $this->assertNull($body['data']['updated_at']);
    }

    public function testCreateReturns422OnMissingName(): void
    {
        $this->apiRequest('POST', '/layout-templates', [
            'template_markup' => '<div>{{ content }}</div>',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns422OnMissingTemplateMarkup(): void
    {
        $this->apiRequest('POST', '/layout-templates', [
            'name' => 'No Markup Template',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturns403ForNonAdminUser(): void
    {
        $this->apiRequest('POST', '/layout-templates', [
            'name'            => 'Forbidden Template',
            'template_markup' => '<div>{{ content }}</div>',
        ], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    // ── GET /layout-templates/:id ──────────────────────────────────────────

    public function testGetByIdReturnsSingleTemplate(): void
    {
        $this->apiRequest('POST', '/layout-templates', [
            'name'            => 'Fetchable Template',
            'positions'       => [['name' => 'content']],
            'template_markup' => '<div>{{ content }}</div>',
        ], $this->adminToken);

        $created = $this->responseJson();
        $id      = $created['data']['id'];

        $this->apiRequest('GET', "/layout-templates/{$id}", [], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame($id, $body['data']['id']);
        $this->assertSame('Fetchable Template', $body['data']['name']);
        $this->assertArrayHasKey('positions', $body['data']);
        $this->assertArrayHasKey('template_markup', $body['data']);
    }

    public function testGetByIdReturns404ForUnknownId(): void
    {
        $this->apiRequest('GET', '/layout-templates/00000000-0000-0000-0000-000000000000', [], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    // ── PATCH /layout-templates/:id ────────────────────────────────────────

    public function testPatchUpdatesTemplate(): void
    {
        $this->apiRequest('POST', '/layout-templates', [
            'name'            => 'Original Template',
            'positions'       => [['name' => 'header']],
            'template_markup' => '<header>{{ header }}</header>',
        ], $this->adminToken);

        $created = $this->responseJson();
        $id      = $created['data']['id'];

        $this->apiRequest('PATCH', "/layout-templates/{$id}", [
            'name'            => 'Updated Template',
            'positions'       => [['name' => 'header'], ['name' => 'footer']],
            'template_markup' => '<header>{{ header }}</header><footer>{{ footer }}</footer>',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame('Updated Template', $body['data']['name']);
        $this->assertCount(2, $body['data']['positions']);
        $this->assertNotNull($body['data']['updated_at']);
    }

    public function testPatchReturns404ForUnknownTemplate(): void
    {
        $this->apiRequest('PATCH', '/layout-templates/00000000-0000-0000-0000-000000000000', [
            'name'            => 'Ghost',
            'positions'       => [],
            'template_markup' => '',
        ], $this->adminToken);

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testPatchReturns403ForNonAdminUser(): void
    {
        $this->apiRequest('POST', '/layout-templates', [
            'name'            => 'Locked Template',
            'positions'       => [],
            'template_markup' => '<div></div>',
        ], $this->adminToken);

        $id = $this->responseJson()['data']['id'];

        $this->apiRequest('PATCH', "/layout-templates/{$id}", [
            'name'            => 'Hacked',
            'positions'       => [],
            'template_markup' => '',
        ], $this->userToken);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }
}
