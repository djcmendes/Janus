<?php

declare(strict_types=1);

namespace App\Notifications\Application\Query\Handler;

use App\Notifications\Application\DTO\NotificationDto;
use App\Notifications\Application\Query\GetNotificationsQuery;
use App\Notifications\Domain\Repository\NotificationRepositoryInterface;

final class GetNotificationsHandler
{
    public function __construct(private readonly NotificationRepositoryInterface $repository) {}

    /** @return array{data: NotificationDto[], total: int} */
    public function handle(GetNotificationsQuery $query): array
    {
        $notifications = $this->repository->findAll($query->limit, $query->offset, $query->recipientId, $query->read);
        $total         = $this->repository->countAll($query->recipientId, $query->read);

        return [
            'data'  => array_map(NotificationDto::fromEntity(...), $notifications),
            'total' => $total,
        ];
    }
}
