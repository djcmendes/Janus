<?php

declare(strict_types=1);

namespace App\Fields\Application\Command\Handler;

use App\Collections\Infrastructure\Service\SchemaManagerService;
use App\Fields\Application\Command\DeleteFieldCommand;
use App\Fields\Domain\Exception\FieldNotFoundException;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;

final class DeleteFieldHandler
{
    public function __construct(
        private readonly FieldMetaRepositoryInterface $repository,
        private readonly SchemaManagerService         $schemaManager,
    ) {}

    /** @throws FieldNotFoundException */
    public function handle(DeleteFieldCommand $command): void
    {
        $field = $this->repository->findByCollectionAndField($command->collection, $command->field);

        if ($field === null) {
            throw new FieldNotFoundException($command->collection, $command->field);
        }

        $this->repository->delete($field);

        if (!$field->getType()->isAlias()) {
            $this->schemaManager->dropColumn($command->collection, $command->field);
        }
    }
}
