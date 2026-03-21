<?php

declare(strict_types=1);

namespace App\Users\Domain\Entity\tests;

class User_softDeleteTest extends UserTestCase
{
    public function test_soft_delete_sets_deleted_at_to_datetime_immutable(): void
    {
        $this->assertNull($this->user->getDeletedAt());
        $this->user->softDelete();
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->user->getDeletedAt());
    }
}
