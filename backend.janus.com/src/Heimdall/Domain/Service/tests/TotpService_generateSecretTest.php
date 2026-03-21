<?php

declare(strict_types=1);

namespace App\Heimdall\Domain\Service\tests;

class TotpService_generateSecretTest extends TotpServiceTestCase
{
    public function test_returns_non_empty_string(): void
    {
        $secret = $this->totp->generateSecret();
        $this->assertNotEmpty($secret);
    }

    public function test_secret_contains_only_valid_base32_chars(): void
    {
        $secret = $this->totp->generateSecret();
        $this->assertMatchesRegularExpression('/^[A-Z2-7]+=*$/', $secret);
    }

    public function test_two_consecutive_secrets_differ(): void
    {
        $a = $this->totp->generateSecret();
        $b = $this->totp->generateSecret();
        $this->assertNotSame($a, $b);
    }
}
