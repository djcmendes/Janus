<?php

declare(strict_types=1);

namespace App\Settings\Domain\Entity\tests;

class Settings_toArrayTest extends SettingsTestCase
{
    public function test_to_array_contains_all_expected_keys(): void
    {
        $arr = $this->settings->toArray();

        $this->assertArrayHasKey('project_name', $arr);
        $this->assertArrayHasKey('default_language', $arr);
        $this->assertArrayHasKey('default_appearance', $arr);
        $this->assertArrayHasKey('project_url', $arr);
        $this->assertArrayHasKey('project_logo', $arr);
        $this->assertArrayHasKey('project_color', $arr);
        $this->assertArrayHasKey('updated_at', $arr);
    }

    public function test_to_array_returns_correct_default_values(): void
    {
        $arr = $this->settings->toArray();

        $this->assertSame('Janus', $arr['project_name']);
        $this->assertSame('en-US', $arr['default_language']);
        $this->assertSame('auto', $arr['default_appearance']);
        $this->assertNull($arr['project_url']);
    }
}
