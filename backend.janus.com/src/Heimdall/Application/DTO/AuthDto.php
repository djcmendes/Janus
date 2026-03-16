<?php

declare(strict_types=1);

namespace App\Heimdall\Application\DTO;

/**
 * DTO for auth login/register requests.
 */
final class LoginRequest
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {}
}

final class RefreshRequest
{
    public function __construct(
        public readonly string $refreshToken,
    ) {}
}

final class PasswordResetRequest
{
    public function __construct(
        public readonly string $email,
    ) {}
}

final class PasswordApplyRequest
{
    public function __construct(
        public readonly string $token,
        public readonly string $password,
    ) {}
}

/**
 * Generic auth response returned after login or token refresh.
 */
final class AuthResponse
{
    public function __construct(
        public readonly string $accessToken,
        public readonly string $tokenType = 'Bearer',
        public readonly int    $expiresIn  = 900,
    ) {}

    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'token_type'   => $this->tokenType,
            'expires_in'   => $this->expiresIn,
        ];
    }
}
