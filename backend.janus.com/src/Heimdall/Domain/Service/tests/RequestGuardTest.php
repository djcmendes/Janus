<?php

declare(strict_types=1);

namespace App\Heimdall\Domain\Service\Test;

use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use App\Heimdall\Domain\Service\RequestGuard;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class RequestGuardTest extends TestCase
{
    private TokenStorageInterface&MockObject $tokenStorage;

    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function makeGuard(string $clientType = 'web'): RequestGuard
    {
        $request = Request::create('/', 'GET', [], [], [], ['HTTP_X_CLIENT_TYPE' => $clientType]);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        return new RequestGuard($this->tokenStorage, $requestStack);
    }

    private function makeAuthenticatedToken(?UserInterface $user = null): TokenInterface&MockObject
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user ?? $this->createMock(UserInterface::class));

        return $token;
    }

    // ── validate_webservice_request ────────────────────────────────────────

    public function test_public_scope_passes_without_authentication(): void
    {
        $this->tokenStorage->expects($this->never())->method('getToken');

        $this->makeGuard()->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::PUBLIC);

        $this->addToAssertionCount(1);
    }

    public function test_authenticated_scope_throws_when_no_token(): void
    {
        $this->tokenStorage->method('getToken')->willReturn(null);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('This endpoint requires authentication.');

        $this->makeGuard()->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
    }

    public function test_authenticated_scope_throws_when_token_has_no_user(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn(null);
        $this->tokenStorage->method('getToken')->willReturn($token);

        $this->expectException(UnauthorizedException::class);

        $this->makeGuard()->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
    }

    public function test_authenticated_scope_passes_when_user_is_set(): void
    {
        $this->tokenStorage->method('getToken')->willReturn($this->makeAuthenticatedToken());

        $this->makeGuard()->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);

        $this->addToAssertionCount(1);
    }

    // ── authorize ─────────────────────────────────────────────────────────

    public function test_authorize_passes_for_allowed_client(): void
    {
        $this->makeGuard('web')->authorize(Client::WEB, Client::IOS);

        $this->addToAssertionCount(1);
    }

    public function test_authorize_throws_for_disallowed_client(): void
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Client "cli" is not authorized to access this endpoint.');

        $this->makeGuard('cli')->authorize(Client::WEB, Client::IOS);
    }

    public function test_authorize_throws_for_unknown_client_header(): void
    {
        $this->expectException(UnauthorizedException::class);

        $this->makeGuard('robot')->authorize(Client::WEB);
    }

    // ── validate_authenticated_user_id ────────────────────────────────────

    public function test_validate_authenticated_user_id_returns_user_id(): void
    {
        $userId = '01950000-0000-7000-0000-000000000001';

        $user = new class($userId) implements UserInterface {
            public function __construct(private readonly string $id) {}
            public function getId(): string { return $this->id; }
            public function getRoles(): array { return ['ROLE_USER']; }
            public function getPassword(): ?string { return null; }
            public function getUserIdentifier(): string { return 'test@janus.com'; }
            public function eraseCredentials(): void {}
        };

        $this->tokenStorage->method('getToken')->willReturn($this->makeAuthenticatedToken($user));

        $result = $this->makeGuard()->validate_authenticated_user_id();

        $this->assertSame($userId, $result);
    }

    public function test_validate_authenticated_user_id_throws_when_unauthenticated(): void
    {
        $this->tokenStorage->method('getToken')->willReturn(null);

        $this->expectException(UnauthorizedException::class);

        $this->makeGuard()->validate_authenticated_user_id();
    }
}
