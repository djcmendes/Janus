<?php

declare(strict_types=1);

namespace App\Notifications\Application\Command\Handler;

use App\Notifications\Application\Command\MarkNotificationReadCommand;
use App\Notifications\Application\DTO\NotificationDto;
use App\Notifications\Domain\Exception\NotificationForbiddenException;
use App\Notifications\Domain\Exception\NotificationNotFoundException;
use App\Notifications\Domain\Repository\NotificationRepositoryInterface;

final class MarkNotificationReadHandler
{
    public function __construct(private readonly NotificationRepositoryInterface $repository) {}

    public function handle(MarkNotificationReadCommand $command): NotificationDto
    {
        $notification = $this->repository->findById($command->id);

        if ($notification === null) {
            throw new NotificationNotFoundException($command->id);
        }

        if (!$command->isAdmin && !$notification->isOwnedBy($command->requestingUserId)) {
            throw new NotificationForbiddenException();
        }

        $notification->markAsRead();

        $this->repository->save($notification);

        return NotificationDto::fromEntity($notification);
    }
}
