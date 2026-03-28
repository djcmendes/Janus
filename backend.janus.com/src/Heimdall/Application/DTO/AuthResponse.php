<?php

declare(strict_types=1);

namespace App\Heimdall\Application\DTO;

/**
 * Generic auth response returned after login or token refresh.
 */
final class AuthResponse
{
    public function __construct(
        public readonly string  $accessToken,
        public readonly string  $tokenType    = 'Bearer',
        public readonly int     $expiresIn    = 900,
        public readonly ?string $refreshToken = null,
    ) {}

    public function toArray(): array
    {
        $data = [
            'data' => [
                'access_token'  => $this->accessToken,
                'token_type'    => $this->tokenType,
                'expires_in'    => $this->expiresIn,
            ],
        ];

        if ($this->refreshToken !== null) {
            $data['data']['refresh_token'] = $this->refreshToken;
        }

        return $data;
    }
}
