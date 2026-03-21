<?php

declare(strict_types=1);

namespace App\Settings\Domain\Entity\tests;

class Settings_settersTest extends SettingsTestCase
{
    public function test_set_project_name_returns_static_and_mutates(): void
    {
        $result = $this->settings->setProjectName('MyApp');
        $this->assertSame($this->settings, $result);
        $this->assertSame('MyApp', $this->settings->getProjectName());
    }

    public function test_set_default_language_returns_static_and_mutates(): void
    {
        $result = $this->settings->setDefaultLanguage('pt-BR');
        $this->assertSame($this->settings, $result);
        $this->assertSame('pt-BR', $this->settings->getDefaultLanguage());
    }

    public function test_set_default_appearance_returns_static_and_mutates(): void
    {
        $result = $this->settings->setDefaultAppearance('dark');
        $this->assertSame($this->settings, $result);
        $this->assertSame('dark', $this->settings->getDefaultAppearance());
    }

    public function test_set_project_url_returns_static_and_mutates(): void
    {
        $result = $this->settings->setProjectUrl('https://example.com');
        $this->assertSame($this->settings, $result);
        $this->assertSame('https://example.com', $this->settings->getProjectUrl());
    }

    public function test_set_project_logo_returns_static_and_mutates(): void
    {
        $result = $this->settings->setProjectLogo('logo.png');
        $this->assertSame($this->settings, $result);
        $this->assertSame('logo.png', $this->settings->getProjectLogo());
    }

    public function test_set_project_color_returns_static_and_mutates(): void
    {
        $result = $this->settings->setProjectColor('#ff0000');
        $this->assertSame($this->settings, $result);
        $this->assertSame('#ff0000', $this->settings->getProjectColor());
    }
}
