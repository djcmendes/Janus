<?php

declare(strict_types=1);

namespace App\Roles\Domain\Entity\tests;

use App\Roles\Domain\Entity\Role;
use PHPUnit\Framework\TestCase;

abstract class RoleTestCase extends TestCase
{
    protected Role $role;

    protected function setUp(): void
    {
        $this->role = new Role('editors');
    }

    protected function tearDown(): void
    {
        unset($this->role);
    }
}
