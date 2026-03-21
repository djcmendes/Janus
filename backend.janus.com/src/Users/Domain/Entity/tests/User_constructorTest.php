<?php

declare(strict_types=1);

namespace App\Users\Domain\Entity\tests;

class User_constructorTest extends UserTestCase
{
    public function test_id_is_not_null(): void
    {
        $this->assertNotNull($this->user->getId());
    }

    public function test_email_matches_constructor_argument(): void
    {
        $this->assertSame('test@example.com', $this->user->getEmail());
    }

    public function test_status_defaults_to_active(): void
    {
        $this->assertSame('active', $this->user->getStatus());
    }

    public function test_totp_enabled_defaults_to_false(): void
    {
        $this->assertFalse($this->user->isTotpEnabled());
    }

    public function test_created_at_is_datetime_immutable(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->user->getCreatedAt());
    }

    public function test_get_roles_always_includes_role_user(): void
    {
        $this->assertContains('ROLE_USER', $this->user->getRoles());
    }
}
