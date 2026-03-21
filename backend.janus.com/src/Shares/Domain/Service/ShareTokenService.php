<?php

declare(strict_types=1);

namespace App\Shares\Domain\Service;

final class ShareTokenService
{
    /** Generates a cryptographically random URL-safe token */
    public function generate(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
