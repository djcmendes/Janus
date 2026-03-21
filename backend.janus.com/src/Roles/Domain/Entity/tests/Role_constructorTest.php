<?php

declare(strict_types=1);

namespace App\Roles\Domain\Entity\tests;

class Role_constructorTest extends RoleTestCase
{
    public function test_id_is_not_null(): void
    {
        $this->assertNotNull($this->role->getId());
    }

    public function test_name_matches_constructor_argument(): void
    {
        $this->assertSame('editors', $this->role->getName());
    }

    public function test_enforce_tfa_defaults_to_false(): void
    {
        $this->assertFalse($this->role->isEnforceTfa());
    }

    public function test_admin_access_defaults_to_false(): void
    {
        $this->assertFalse($this->role->isAdminAccess());
    }

    public function test_app_access_defaults_to_true(): void
    {
        $this->assertTrue($this->role->isAppAccess());
    }
}
