<?php

declare(strict_types=1);

namespace App\Notifications\Domain\Repository;

use App\Notifications\Domain\Entity\Notification;

interface NotificationRepositoryInterface
{
    public function save(Notification $notification): void;

    public function delete(Notification $notification): void;

    public function findById(string $id): ?Notification;

    /** @return Notification[] */
    public function findAll(
        int     $limit,
        int     $offset,
        ?string $recipientId = null,
        ?bool   $read        = null,
    ): array;

    public function countAll(
        ?string $recipientId = null,
        ?bool   $read        = null,
    ): int;
}
