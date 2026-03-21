<?php

declare(strict_types=1);

namespace App\Roles\Domain\Entity\tests;

class Role_settersTest extends RoleTestCase
{
    public function test_set_name_returns_static_and_mutates(): void
    {
        $result = $this->role->setName('admins');
        $this->assertSame($this->role, $result);
        $this->assertSame('admins', $this->role->getName());
    }

    public function test_set_description_returns_static_and_mutates(): void
    {
        $result = $this->role->setDescription('Content editors');
        $this->assertSame($this->role, $result);
        $this->assertSame('Content editors', $this->role->getDescription());
    }

    public function test_set_icon_returns_static_and_mutates(): void
    {
        $result = $this->role->setIcon('edit');
        $this->assertSame($this->role, $result);
        $this->assertSame('edit', $this->role->getIcon());
    }

    public function test_set_enforce_tfa_returns_static_and_mutates(): void
    {
        $result = $this->role->setEnforceTfa(true);
        $this->assertSame($this->role, $result);
        $this->assertTrue($this->role->isEnforceTfa());
    }

    public function test_set_admin_access_returns_static_and_mutates(): void
    {
        $result = $this->role->setAdminAccess(true);
        $this->assertSame($this->role, $result);
        $this->assertTrue($this->role->isAdminAccess());
    }

    public function test_set_app_access_returns_static_and_mutates(): void
    {
        $result = $this->role->setAppAccess(false);
        $this->assertSame($this->role, $result);
        $this->assertFalse($this->role->isAppAccess());
    }
}
