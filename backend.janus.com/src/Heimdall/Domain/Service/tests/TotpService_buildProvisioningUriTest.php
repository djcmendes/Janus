<?php

declare(strict_types=1);

namespace App\Heimdall\Domain\Service\tests;

class TotpService_buildProvisioningUriTest extends TotpServiceTestCase
{
    public function test_uri_starts_with_otpauth_totp(): void
    {
        $secret = $this->totp->generateSecret();
        $uri    = $this->totp->buildProvisioningUri('user@example.com', $secret);
        $this->assertStringStartsWith('otpauth://totp/', $uri);
    }

    public function test_uri_contains_janus_issuer(): void
    {
        $secret = $this->totp->generateSecret();
        $uri    = $this->totp->buildProvisioningUri('user@example.com', $secret);
        $this->assertStringContainsString('Janus', $uri);
    }

    public function test_uri_contains_email(): void
    {
        $secret = $this->totp->generateSecret();
        $uri    = $this->totp->buildProvisioningUri('user@example.com', $secret);
        $this->assertStringContainsString('user%40example.com', $uri);
    }

    public function test_uri_contains_secret_param(): void
    {
        $secret = $this->totp->generateSecret();
        $uri    = $this->totp->buildProvisioningUri('user@example.com', $secret);
        $this->assertStringContainsString('secret=', $uri);
    }
}
