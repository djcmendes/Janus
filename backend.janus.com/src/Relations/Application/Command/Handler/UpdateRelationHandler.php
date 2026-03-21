<?php

declare(strict_types=1);

namespace App\Relations\Application\Command\Handler;

use App\Relations\Application\Command\UpdateRelationCommand;
use App\Relations\Application\DTO\RelationDto;
use App\Relations\Domain\Exception\RelationNotFoundException;
use App\Relations\Domain\Repository\RelationRepositoryInterface;

final class UpdateRelationHandler
{
    public function __construct(
        private readonly RelationRepositoryInterface $repository,
    ) {}

    /** @throws RelationNotFoundException */
    public function handle(UpdateRelationCommand $command): RelationDto
    {
        $relation = $this->repository->findByCollectionAndField($command->manyCollection, $command->manyField);

        if ($relation === null) {
            throw new RelationNotFoundException($command->manyCollection, $command->manyField);
        }

        if ($command->oneCollection !== UpdateRelationCommand::UNCHANGED) {
            $relation->setOneCollection($command->oneCollection);
        }
        if ($command->oneField !== UpdateRelationCommand::UNCHANGED) {
            $relation->setOneField($command->oneField);
        }
        if ($command->junctionCollection !== UpdateRelationCommand::UNCHANGED) {
            $relation->setJunctionCollection($command->junctionCollection);
        }

        $this->repository->save($relation);

        return RelationDto::fromEntity($relation);
    }
}
