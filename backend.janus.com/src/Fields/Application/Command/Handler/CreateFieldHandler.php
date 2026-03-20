<?php

declare(strict_types=1);

namespace App\Fields\Application\Command\Handler;

use App\Collections\Domain\Exception\CollectionNotFoundException;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Collections\Infrastructure\Service\SchemaManagerService;
use App\Fields\Application\Command\CreateFieldCommand;
use App\Fields\Application\DTO\FieldDto;
use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Enum\FieldType;
use App\Fields\Domain\Exception\FieldAlreadyExistsException;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;

final class CreateFieldHandler
{
    public function __construct(
        private readonly FieldMetaRepositoryInterface      $fieldRepository,
        private readonly CollectionMetaRepositoryInterface $collectionRepository,
        private readonly SchemaManagerService              $schemaManager,
    ) {}

    /**
     * @throws CollectionNotFoundException
     * @throws FieldAlreadyExistsException
     * @throws \InvalidArgumentException
     */
    public function handle(CreateFieldCommand $command): FieldDto
    {
        if ($this->collectionRepository->findByName($command->collection) === null) {
            throw new CollectionNotFoundException($command->collection);
        }

        if ($this->fieldRepository->findByCollectionAndField($command->collection, $command->field) !== null) {
            throw new FieldAlreadyExistsException($command->collection, $command->field);
        }

        $type = FieldType::from($command->type);

        $fieldMeta = new FieldMeta($command->collection, $command->field, $type);
        $fieldMeta->setLabel($command->label);
        $fieldMeta->setNote($command->note);
        $fieldMeta->setRequired($command->required);
        $fieldMeta->setReadonly($command->readonly);
        $fieldMeta->setHidden($command->hidden);
        $fieldMeta->setSortOrder($command->sortOrder);

        if (!$type->isAlias()) {
            $this->schemaManager->addColumn($command->collection, $command->field, $type->toColumnDdl());
        }

        $this->fieldRepository->save($fieldMeta);

        return FieldDto::fromEntity($fieldMeta);
    }
}
