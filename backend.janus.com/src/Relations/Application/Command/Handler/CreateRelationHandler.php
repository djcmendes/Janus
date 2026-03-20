<?php

declare(strict_types=1);

namespace App\Relations\Application\Command\Handler;

use App\Relations\Application\Command\CreateRelationCommand;
use App\Relations\Application\DTO\RelationDto;
use App\Relations\Domain\Entity\Relation;
use App\Relations\Domain\Exception\RelationAlreadyExistsException;
use App\Relations\Domain\Repository\RelationRepositoryInterface;

final class CreateRelationHandler
{
    public function __construct(
        private readonly RelationRepositoryInterface $repository,
    ) {}

    /** @throws RelationAlreadyExistsException */
    public function handle(CreateRelationCommand $command): RelationDto
    {
        if ($this->repository->findByCollectionAndField($command->manyCollection, $command->manyField) !== null) {
            throw new RelationAlreadyExistsException($command->manyCollection, $command->manyField);
        }

        $relation = new Relation($command->manyCollection, $command->manyField);
        $relation->setOneCollection($command->oneCollection);
        $relation->setOneField($command->oneField);
        $relation->setJunctionCollection($command->junctionCollection);

        $this->repository->save($relation);

        return RelationDto::fromEntity($relation);
    }
}
