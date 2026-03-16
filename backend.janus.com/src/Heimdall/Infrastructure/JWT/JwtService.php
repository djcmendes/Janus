<?php

declare(strict_types=1);

namespace App\Heimdall\Infrastructure\JWT;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Thin wrapper around the LexikJWT token manager that
 * adds Janus-specific claims to access tokens.
 */
final class JwtService
{
    public function __construct(
        private readonly JWTTokenManagerInterface $jwtManager,
    ) {}

    /**
     * Issues a new access token for the given user.
     */
    public function issueAccessToken(UserInterface $user): string
    {
        return $this->jwtManager->create($user);
    }

    /**
     * Parses a raw JWT string and returns its payload as an array,
     * or null if the token is invalid / expired.
     */
    public function decode(string $token): ?array
    {
        try {
            return $this->jwtManager->parse($token);
        } catch (\Throwable) {
            return null;
        }
    }
}
