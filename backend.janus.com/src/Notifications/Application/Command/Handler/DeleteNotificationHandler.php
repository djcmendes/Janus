<?php

declare(strict_types=1);

namespace App\Notifications\Application\Command\Handler;

use App\Notifications\Application\Command\DeleteNotificationCommand;
use App\Notifications\Domain\Exception\NotificationForbiddenException;
use App\Notifications\Domain\Exception\NotificationNotFoundException;
use App\Notifications\Domain\Repository\NotificationRepositoryInterface;

final class DeleteNotificationHandler
{
    public function __construct(private readonly NotificationRepositoryInterface $repository) {}

    public function handle(DeleteNotificationCommand $command): void
    {
        $notification = $this->repository->findById($command->id);

        if ($notification === null) {
            throw new NotificationNotFoundException($command->id);
        }

        if (!$command->isAdmin && !$notification->isOwnedBy($command->requestingUserId)) {
            throw new NotificationForbiddenException();
        }

        $this->repository->delete($notification);
    }
}
