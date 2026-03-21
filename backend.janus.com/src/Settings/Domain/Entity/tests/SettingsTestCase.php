<?php

declare(strict_types=1);

namespace App\Settings\Domain\Entity\tests;

use App\Settings\Domain\Entity\Settings;
use PHPUnit\Framework\TestCase;

abstract class SettingsTestCase extends TestCase
{
    protected Settings $settings;

    protected function setUp(): void
    {
        $this->settings = new Settings();
    }

    protected function tearDown(): void
    {
        unset($this->settings);
    }
}
