<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Heimdall\Infrastructure\JWT\JwtService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Feature tests for:
 *   POST /auth/login
 *   POST /auth/refresh
 *   POST /auth/logout
 *   GET  /auth/me
 */
final class AuthTest extends ApiTestCase
{
    // ── POST /auth/login ───────────────────────────────────────────────────

    public function testLoginReturnsTokensOnValidCredentials(): void
    {
        $this->createUser('user@example.com', 'password123');

        $this->apiRequest('POST', '/auth/login', [
            'email'    => 'user@example.com',
            'password' => 'password123',
        ]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('access_token', $body['data']);
        $this->assertArrayHasKey('refresh_token', $body['data']);
        $this->assertSame('Bearer', $body['data']['token_type']);
    }

    public function testLoginReturns401OnBadPassword(): void
    {
        $this->createUser('user@example.com', 'correct-password');

        $this->apiRequest('POST', '/auth/login', [
            'email'    => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testLoginReturns401OnUnknownEmail(): void
    {
        $this->apiRequest('POST', '/auth/login', [
            'email'    => 'nobody@example.com',
            'password' => 'anything',
        ]);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testLoginReturns400OnMissingCredentials(): void
    {
        $this->apiRequest('POST', '/auth/login', []);

        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    // ── POST /auth/refresh ─────────────────────────────────────────────────

    public function testRefreshReturnsNewTokensOnValidRefreshToken(): void
    {
        $user = $this->createUser('user@example.com', 'pass');

        /** @var JwtService $jwt */
        $jwt          = static::getContainer()->get(JwtService::class);
        $refreshToken = $jwt->issueRefreshToken($user);

        $this->apiRequest('POST', '/auth/refresh', ['refresh_token' => $refreshToken]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertArrayHasKey('access_token', $body['data']);
        $this->assertArrayHasKey('refresh_token', $body['data']);
    }

    public function testRefreshReturns401OnInvalidToken(): void
    {
        $this->apiRequest('POST', '/auth/refresh', ['refresh_token' => 'invalid.token.value']);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testRefreshReturns401OnAccessTokenUsedAsRefreshToken(): void
    {
        $user = $this->createUser('user@example.com', 'pass');

        /** @var JwtService $jwt */
        $jwt         = static::getContainer()->get(JwtService::class);
        $accessToken = $jwt->issueAccessToken($user);

        // Access tokens have type=access, not type=refresh — must be rejected
        $this->apiRequest('POST', '/auth/refresh', ['refresh_token' => $accessToken]);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    public function testRefreshReturns400OnMissingToken(): void
    {
        $this->apiRequest('POST', '/auth/refresh', []);

        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    // ── POST /auth/logout ──────────────────────────────────────────────────

    public function testLogoutReturns200(): void
    {
        $user  = $this->createUser('user@example.com', 'pass');
        $token = $this->getToken($user);

        $this->apiRequest('POST', '/auth/logout', [], $token);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertStringContainsString('Logged out', $body['data']['message']);
    }

    // ── GET /auth/me ───────────────────────────────────────────────────────

    public function testMeReturnsAuthenticatedUserData(): void
    {
        $user  = $this->createUser('me@example.com', 'pass', ['ROLE_USER']);
        $token = $this->getToken($user);

        $this->apiRequest('GET', '/auth/me', [], $token);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $body = $this->responseJson();
        $this->assertSame('me@example.com', $body['data']['email']);
        $this->assertContains('ROLE_USER', $body['data']['roles']);
    }

    public function testMeReturns401WithoutToken(): void
    {
        $this->apiRequest('GET', '/auth/me');

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }
}
