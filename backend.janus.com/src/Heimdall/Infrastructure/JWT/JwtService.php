<?php

declare(strict_types=1);

namespace App\Heimdall\Infrastructure\JWT;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
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
        private readonly JWTEncoderInterface      $jwtEncoder,
        private readonly int                      $refreshTtl = 604800,
    ) {}

    /**
     * Issues a short-lived access token for the given user.
     */
    public function issueAccessToken(UserInterface $user): string
    {
        return $this->jwtManager->create($user);
    }

    /**
     * Issues a long-lived refresh token with a `type=refresh` claim.
     */
    public function issueRefreshToken(UserInterface $user): string
    {
        return $this->jwtEncoder->encode([
            'sub'  => $user->getUserIdentifier(),
            'type' => 'refresh',
            'iat'  => time(),
            'exp'  => time() + $this->refreshTtl,
        ]);
    }

    /**
     * Issues a short-lived password-reset token (1 hour TTL) with a `type=reset` claim.
     */
    public function issuePasswordResetToken(UserInterface $user): string
    {
        return $this->jwtEncoder->encode([
            'sub'  => $user->getUserIdentifier(),
            'type' => 'reset',
            'iat'  => time(),
            'exp'  => time() + 3600,
        ]);
    }

    /**
     * Decodes a typed token (refresh or reset). Returns the subject (email) if the
     * token is valid and has the expected type; null otherwise.
     */
    public function decodeTokenOfType(string $token, string $type): ?string
    {
        try {
            $payload = $this->jwtEncoder->decode($token);
        } catch (\Throwable) {
            return null;
        }

        if (($payload['type'] ?? '') !== $type) {
            return null;
        }

        return $payload['sub'] ?? null;
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
