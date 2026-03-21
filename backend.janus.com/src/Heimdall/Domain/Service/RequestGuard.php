<?php

declare(strict_types=1);

namespace App\Heimdall\Domain\Service;

use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * The RequestGuard is injected into controllers and validates each
 * incoming request against API version, allowed clients, and auth scope.
 *
 * Usage in a controller:
 *
 *   $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
 *   $this->guard->authorize(Client::WEB, Client::IOS);
 *   $userId = $this->guard->validate_authenticated_user_id();
 */
final class RequestGuard
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly RequestStack          $requestStack,
    ) {}

    /**
     * Validates that the request matches the expected API version and scope.
     *
     * @throws UnauthorizedException if the scope requires authentication and no user is logged in
     */
    public function validate_webservice_request(ApiVersion $version, ApiScope $scope): void
    {
        if ($scope === ApiScope::AUTHENTICATED) {
            $this->requireAuthentication();
        }
    }

    /**
     * Checks that the requesting client type is in the allowed list.
     *
     * @throws UnauthorizedException if the client is not permitted
     */
    public function authorize(Client ...$allowedClients): void
    {
        $request      = $this->requestStack->getCurrentRequest();
        $clientHeader = $request?->headers->get('X-Client-Type', Client::WEB->value) ?? Client::WEB->value;

        $requestingClient = Client::tryFrom($clientHeader);

        if ($requestingClient === null || !in_array($requestingClient, $allowedClients, true)) {
            throw new UnauthorizedException(
                sprintf('Client "%s" is not authorized to access this endpoint.', $clientHeader)
            );
        }
    }

    /**
     * Returns the authenticated user's ID, or throws if unauthenticated.
     *
     * @throws UnauthorizedException
     */
    public function validate_authenticated_user_id(): string
    {
        $this->requireAuthentication();

        $token = $this->tokenStorage->getToken();
        $user  = $token?->getUser();

        if ($user === null || !method_exists($user, 'getId')) {
            throw new UnauthorizedException('Cannot resolve authenticated user identity.');
        }

        return (string) $user->getId();
    }

    /**
     * A simpler alias when you just need the user ID without
     * re-checking full authentication (the route guard already did it).
     *
     * @throws UnauthorizedException if there is no authenticated user
     */
    public function validate_user_id(): string
    {
        return $this->validate_authenticated_user_id();
    }

    // ── Private helpers ────────────────────────────────────────────────────

    private function requireAuthentication(): void
    {
        $token = $this->tokenStorage->getToken();

        if ($token === null || $token->getUser() === null) {
            throw new UnauthorizedException('This endpoint requires authentication.');
        }
    }
}
