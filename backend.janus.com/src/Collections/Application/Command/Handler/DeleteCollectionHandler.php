<?php

declare(strict_types=1);

namespace App\Collections\Application\Command\Handler;

use App\Collections\Application\Command\DeleteCollectionCommand;
use App\Collections\Domain\Exception\CollectionNotFoundException;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Collections\Infrastructure\Service\SchemaManagerService;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;

final class DeleteCollectionHandler
{
    public function __construct(
        private readonly CollectionMetaRepositoryInterface $repository,
        private readonly SchemaManagerService              $schemaManager,
        private readonly FieldMetaRepositoryInterface      $fieldRepository,
    ) {}

    /** @throws CollectionNotFoundException */
    public function handle(DeleteCollectionCommand $command): void
    {
        $collection = $this->repository->findByName($command->name);

        if ($collection === null) {
            throw new CollectionNotFoundException($command->name);
        }

        $this->fieldRepository->deleteByCollection($command->name);
        $this->repository->delete($collection);
        $this->schemaManager->dropTable($command->name);
    }
}
