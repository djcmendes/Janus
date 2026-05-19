<?php

declare(strict_types=1);

namespace App\Heimdall\Application\Service;

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
 *   $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
 *   $this->guard->authorize(Client::WEB, Client::IOS);
 *   $userId = $this->guard->validateAuthenticatedUserId();
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
    public function validateWebserviceRequest(ApiVersion $version, ApiScope $scope): void
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
    public function validateAuthenticatedUserId(): string
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
    public function validateUserId(): string
    {
        return $this->validateAuthenticatedUserId();
    }

    // ── Private helpers ────────────────────────────────────────────────────

    private function requireAuthentication(): void
    {
        $token = $this->tokenStorage->getToken();

        if ($token?->getUser() === null) {
            throw new UnauthorizedException('This endpoint requires authentication.');
        }
    }
}
