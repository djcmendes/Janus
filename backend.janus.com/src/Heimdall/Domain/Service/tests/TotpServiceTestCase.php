<?php

declare(strict_types=1);

namespace App\Heimdall\Domain\Service\tests;

use App\Heimdall\Domain\Service\TotpService;
use PHPUnit\Framework\TestCase;

abstract class TotpServiceTestCase extends TestCase
{
    protected TotpService $totp;

    protected function setUp(): void
    {
        $this->totp = new TotpService();
    }

    protected function tearDown(): void
    {
        unset($this->totp);
    }
}
