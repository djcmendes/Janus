<?php

/**
 * @file UpdateFieldHandler.php
 *
 * CQRS command handler for partially updating a field metadata record.
 * Uses the UpdateFieldCommand UNCHANGED sentinel to skip unmodified fields.
 *
 * @package App\Fields\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Application\Command\Handler;

use App\Fields\Application\Command\UpdateFieldCommand;
use App\Fields\Application\DTO\FieldDto;
use App\Fields\Domain\Exception\FieldNotFoundException;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;

/**
 * Handles UpdateFieldCommand — applies partial mutations and persists the updated FieldMeta.
 */
final class UpdateFieldHandler
{
    /**
     * @param FieldMetaRepositoryInterface $repository Field persistence store.
     */
    public function __construct(
        private readonly FieldMetaRepositoryInterface $repository,
    ) {}

    /**
     * Applies partial updates to an existing FieldMeta record.
     *
     * Fields whose command value equals UpdateFieldCommand::UNCHANGED are skipped.
     * Nullable fields (required, readonly, hidden, sortOrder) are skipped when null.
     *
     * @param  UpdateFieldCommand $command Partial update payload.
     * @return FieldDto                    The updated field as a read model.
     *
     * @throws FieldNotFoundException When no field with the given name exists in the collection.
     */
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
