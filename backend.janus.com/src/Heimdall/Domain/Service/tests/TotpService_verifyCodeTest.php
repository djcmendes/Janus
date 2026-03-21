<?php

declare(strict_types=1);

namespace App\Heimdall\Domain\Service\tests;

use OTPHP\TOTP;

class TotpService_verifyCodeTest extends TotpServiceTestCase
{
    public function test_valid_current_code_returns_true(): void
    {
        $secret  = $this->totp->generateSecret();
        $totp    = TOTP::createFromSecret($secret);
        $code    = $totp->now();

        $this->assertTrue($this->totp->verifyCode($secret, $code));
    }

    public function test_all_zeros_code_returns_false(): void
    {
        $secret = $this->totp->generateSecret();
        $this->assertFalse($this->totp->verifyCode($secret, '000000'));
    }
}
