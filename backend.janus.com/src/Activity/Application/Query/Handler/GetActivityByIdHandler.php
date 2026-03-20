<?php

declare(strict_types=1);

namespace App\Activity\Application\Query\Handler;

use App\Activity\Application\DTO\ActivityDto;
use App\Activity\Application\Query\GetActivityByIdQuery;
use App\Activity\Domain\Exception\ActivityNotFoundException;
use App\Activity\Domain\Repository\ActivityRepositoryInterface;

final class GetActivityByIdHandler
{
    public function __construct(
        private readonly ActivityRepositoryInterface $repository,
    ) {}

    /** @throws ActivityNotFoundException */
    public function handle(GetActivityByIdQuery $query): ActivityDto
    {
        $activity = $this->repository->findById($query->id);

        if ($activity === null) {
            throw new ActivityNotFoundException($query->id);
        }

        return ActivityDto::fromEntity($activity);
    }
}
