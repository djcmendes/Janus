<?php

declare(strict_types=1);

namespace App\Settings\Domain\Entity\tests;

class Settings_constructorTest extends SettingsTestCase
{
    public function test_default_project_name_is_janus(): void
    {
        $this->assertSame('Janus', $this->settings->getProjectName());
    }

    public function test_default_language_is_en_us(): void
    {
        $this->assertSame('en-US', $this->settings->getDefaultLanguage());
    }

    public function test_default_appearance_is_auto(): void
    {
        $this->assertSame('auto', $this->settings->getDefaultAppearance());
    }

    public function test_default_project_url_is_null(): void
    {
        $this->assertNull($this->settings->getProjectUrl());
    }
}
