<?php

declare(strict_types=1);

namespace App\Users\Domain\Entity\tests;

class User_totpTest extends UserTestCase
{
    public function test_enable_totp_sets_secret_and_enabled(): void
    {
        $this->user->enableTotp('MYSECRET');
        $this->assertTrue($this->user->isTotpEnabled());
        $this->assertSame('MYSECRET', $this->user->getTotpSecret());
    }

    public function test_disable_totp_clears_secret_and_enabled(): void
    {
        $this->user->enableTotp('MYSECRET');
        $this->user->disableTotp();
        $this->assertFalse($this->user->isTotpEnabled());
        $this->assertNull($this->user->getTotpSecret());
    }

    public function test_store_totp_secret_sets_secret_without_enabling(): void
    {
        $this->user->storeTotpSecret('PENDINGSECRET');
        $this->assertFalse($this->user->isTotpEnabled());
        $this->assertSame('PENDINGSECRET', $this->user->getTotpSecret());
    }
}
