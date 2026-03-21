<?php

declare(strict_types=1);

namespace App\Relations\Application\Command\Handler;

use App\Relations\Application\Command\DeleteRelationCommand;
use App\Relations\Domain\Exception\RelationNotFoundException;
use App\Relations\Domain\Repository\RelationRepositoryInterface;

final class DeleteRelationHandler
{
    public function __construct(
        private readonly RelationRepositoryInterface $repository,
    ) {}

    /** @throws RelationNotFoundException */
    public function handle(DeleteRelationCommand $command): void
    {
        $relation = $this->repository->findByCollectionAndField($command->manyCollection, $command->manyField);

        if ($relation === null) {
            throw new RelationNotFoundException($command->manyCollection, $command->manyField);
        }

        $this->repository->delete($relation);
    }
}
