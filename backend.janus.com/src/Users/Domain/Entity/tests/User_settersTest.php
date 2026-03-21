<?php

declare(strict_types=1);

namespace App\Users\Domain\Entity\tests;

class User_settersTest extends UserTestCase
{
    public function test_set_email_returns_static_and_mutates(): void
    {
        $result = $this->user->setEmail('new@example.com');
        $this->assertSame($this->user, $result);
        $this->assertSame('new@example.com', $this->user->getEmail());
    }

    public function test_set_first_name_returns_static_and_mutates(): void
    {
        $result = $this->user->setFirstName('Alice');
        $this->assertSame($this->user, $result);
        $this->assertSame('Alice', $this->user->getFirstName());
    }

    public function test_set_last_name_returns_static_and_mutates(): void
    {
        $result = $this->user->setLastName('Smith');
        $this->assertSame($this->user, $result);
        $this->assertSame('Smith', $this->user->getLastName());
    }

    public function test_set_status_returns_static_and_mutates(): void
    {
        $result = $this->user->setStatus('suspended');
        $this->assertSame($this->user, $result);
        $this->assertSame('suspended', $this->user->getStatus());
    }

    public function test_set_roles_returns_static_and_mutates(): void
    {
        $result = $this->user->setRoles(['ROLE_ADMIN']);
        $this->assertSame($this->user, $result);
        $this->assertContains('ROLE_ADMIN', $this->user->getRoles());
    }

    public function test_get_roles_deduplicates_role_user(): void
    {
        $this->user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $roles = $this->user->getRoles();
        $this->assertSame(array_unique($roles), $roles);
    }
}
