<?php

declare(strict_types=1);

namespace App\Notifications\Application\Command\Handler;

use App\Notifications\Application\Command\CreateNotificationCommand;
use App\Notifications\Application\DTO\NotificationDto;
use App\Notifications\Domain\Entity\Notification;
use App\Notifications\Domain\Repository\NotificationRepositoryInterface;

final class CreateNotificationHandler
{
    public function __construct(private readonly NotificationRepositoryInterface $repository) {}

    public function handle(CreateNotificationCommand $command): NotificationDto
    {
        $notification = new Notification(
            $command->recipientId,
            $command->subject,
            $command->message,
            $command->senderId,
            $command->collection,
            $command->item,
        );

        $this->repository->save($notification);

        return NotificationDto::fromEntity($notification);
    }
}
