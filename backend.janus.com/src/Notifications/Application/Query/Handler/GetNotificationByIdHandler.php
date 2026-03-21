<?php

declare(strict_types=1);

namespace App\Notifications\Application\Query\Handler;

use App\Notifications\Application\DTO\NotificationDto;
use App\Notifications\Application\Query\GetNotificationByIdQuery;
use App\Notifications\Domain\Exception\NotificationNotFoundException;
use App\Notifications\Domain\Repository\NotificationRepositoryInterface;

final class GetNotificationByIdHandler
{
    public function __construct(private readonly NotificationRepositoryInterface $repository) {}

    public function handle(GetNotificationByIdQuery $query): NotificationDto
    {
        $notification = $this->repository->findById($query->id);

        if ($notification === null) {
            throw new NotificationNotFoundException($query->id);
        }

        return NotificationDto::fromEntity($notification);
    }
}
