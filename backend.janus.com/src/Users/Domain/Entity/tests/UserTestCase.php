<?php

declare(strict_types=1);

namespace App\Users\Domain\Entity\tests;

use App\Users\Domain\Entity\User;
use PHPUnit\Framework\TestCase;

abstract class UserTestCase extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        $this->user = new User('test@example.com');
    }

    protected function tearDown(): void
    {
        unset($this->user);
    }
}
