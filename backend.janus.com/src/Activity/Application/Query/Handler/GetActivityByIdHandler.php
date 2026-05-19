<?php

/**
 * @file GetActivityByIdHandler.php
 *
 * Query handler that retrieves a single Activity record by UUID.
 *
 * @package App\Activity\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Query\Handler;

use App\Activity\Application\DTO\ActivityDto;
use App\Activity\Application\Query\GetActivityByIdQuery;
use App\Activity\Domain\Exception\ActivityNotFoundException;
use App\Activity\Domain\Repository\ActivityRepositoryInterface;

/**
 * Handles GetActivityByIdQuery, loading the matching Activity entity and
 * converting it to an ActivityDto, or throwing if no record exists.
 */
final readonly class GetActivityByIdHandler
{
    /**
     * Constructor
     *
     * @param ActivityRepositoryInterface $repository Repository used to load Activity entities.
     */
    public function __construct(
        private ActivityRepositoryInterface $repository,
    ) {}

    /**
     * Loads the Activity record for the given UUID and returns it as a DTO.
     *
     * @param  GetActivityByIdQuery $query Query carrying the target UUID.
     * @return ActivityDto Serialisable representation of the found record.
     * @throws ActivityNotFoundException When no Activity exists for the given UUID.
     */
    public function handle(GetActivityByIdQuery $query): ActivityDto
    {
        $activity = $this->repository->findById(id: $query->id);

        if ($activity === null) {
            throw new ActivityNotFoundException(id: $query->id);
        }

        return ActivityDto::fromEntity(activity: $activity);
    }
}
