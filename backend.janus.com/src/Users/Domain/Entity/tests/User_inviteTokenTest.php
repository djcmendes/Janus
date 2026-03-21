<?php

declare(strict_types=1);

namespace App\Users\Domain\Entity\tests;

class User_inviteTokenTest extends UserTestCase
{
    public function test_valid_future_token_is_valid(): void
    {
        $future = new \DateTimeImmutable('+1 hour');
        $this->user->setInviteToken('tok123', $future);
        $this->assertTrue($this->user->isInviteTokenValid());
    }

    public function test_expired_past_token_is_invalid(): void
    {
        $past = new \DateTimeImmutable('-1 hour');
        $this->user->setInviteToken('tok123', $past);
        $this->assertFalse($this->user->isInviteTokenValid());
    }

    public function test_cleared_token_is_invalid(): void
    {
        $future = new \DateTimeImmutable('+1 hour');
        $this->user->setInviteToken('tok123', $future);
        $this->user->clearInviteToken();
        $this->assertFalse($this->user->isInviteTokenValid());
    }
}
