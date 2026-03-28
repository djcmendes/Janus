<?php

declare(strict_types=1);

namespace App\Collections\Application\Command\Handler;

use App\Collections\Application\Command\CreateCollectionCommand;
use App\Collections\Application\DTO\CollectionDto;
use App\Collections\Domain\Entity\CollectionMeta;
use App\Collections\Domain\Exception\CollectionAlreadyExistsException;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Collections\Infrastructure\Service\SchemaManagerService;
use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Enum\FieldType;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;

final class CreateCollectionHandler
{
    public function __construct(
        private readonly CollectionMetaRepositoryInterface $repository,
        private readonly FieldMetaRepositoryInterface      $fieldRepository,
        private readonly SchemaManagerService              $schemaManager,
    ) {}

    /** @throws CollectionAlreadyExistsException */
    public function handle(CreateCollectionCommand $command): CollectionDto
    {
        if ($this->repository->findByName($command->name) !== null) {
            throw new CollectionAlreadyExistsException($command->name);
        }

        $collection = new CollectionMeta($command->name);
        $collection->setLabel($command->label);
        $collection->setIcon($command->icon);
        $collection->setNote($command->note);
        $collection->setHidden($command->hidden);
        $collection->setSingleton($command->singleton);
        $collection->setSortField($command->sortField);

        $this->schemaManager->createTable($command->name, $command->primaryKeyField, $command->primaryKeyType);

        $pkType    = FieldType::from($command->primaryKeyType);
        $pkField   = new FieldMeta($command->name, $command->primaryKeyField, $pkType);
        $pkField->setHidden(true);
        $pkField->setReadonly(true);

        $this->fieldRepository->save($pkField, false);
        $this->repository->save($collection);

        return CollectionDto::fromEntity($collection);
    }
}
