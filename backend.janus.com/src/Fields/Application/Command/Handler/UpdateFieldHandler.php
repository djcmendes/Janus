<?php

declare(strict_types=1);

namespace App\Fields\Application\Command\Handler;

use App\Fields\Application\Command\UpdateFieldCommand;
use App\Fields\Application\DTO\FieldDto;
use App\Fields\Domain\Exception\FieldNotFoundException;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;

final class UpdateFieldHandler
{
    public function __construct(
        private readonly FieldMetaRepositoryInterface $repository,
    ) {}

    /** @throws FieldNotFoundException */
    public function handle(UpdateFieldCommand $command): FieldDto
    {
        $field = $this->repository->findByCollectionAndField($command->collection, $command->field);

        if ($field === null) {
            throw new FieldNotFoundException($command->collection, $command->field);
        }

        if ($command->label !== UpdateFieldCommand::UNCHANGED) {
            $field->setLabel($command->label);
        }
        if ($command->note !== UpdateFieldCommand::UNCHANGED) {
            $field->setNote($command->note);
        }
        if ($command->required !== null) {
            $field->setRequired($command->required);
        }
        if ($command->readonly !== null) {
            $field->setReadonly($command->readonly);
        }
        if ($command->hidden !== null) {
            $field->setHidden($command->hidden);
        }
        if ($command->sortOrder !== null) {
            $field->setSortOrder($command->sortOrder);
        }
        if ($command->interface !== UpdateFieldCommand::UNCHANGED) {
            $field->setInterface($command->interface);
        }
        if ($command->options !== UpdateFieldCommand::UNCHANGED) {
            $field->setOptions(is_array($command->options) ? $command->options : null);
        }

        $this->repository->save($field);

        return FieldDto::fromEntity($field);
    }
}
