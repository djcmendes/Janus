<?php

declare(strict_types=1);

namespace App\Schema\Application\Command\Handler;

use App\Collections\Application\Command\CreateCollectionCommand;
use App\Collections\Application\Command\DeleteCollectionCommand;
use App\Collections\Application\Command\Handler\CreateCollectionHandler;
use App\Collections\Application\Command\Handler\DeleteCollectionHandler;
use App\Collections\Application\Command\Handler\UpdateCollectionHandler;
use App\Collections\Application\Command\UpdateCollectionCommand;
use App\Collections\Domain\Exception\CollectionAlreadyExistsException;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Fields\Application\Command\CreateFieldCommand;
use App\Fields\Application\Command\DeleteFieldCommand;
use App\Fields\Application\Command\Handler\CreateFieldHandler;
use App\Fields\Application\Command\Handler\DeleteFieldHandler;
use App\Fields\Application\Command\Handler\UpdateFieldHandler;
use App\Fields\Application\Command\UpdateFieldCommand;
use App\Fields\Domain\Exception\FieldAlreadyExistsException;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;
use App\Relations\Application\Command\CreateRelationCommand;
use App\Relations\Application\Command\DeleteRelationCommand;
use App\Relations\Application\Command\Handler\CreateRelationHandler;
use App\Relations\Application\Command\Handler\DeleteRelationHandler;
use App\Relations\Domain\Exception\RelationAlreadyExistsException;
use App\Relations\Domain\Repository\RelationRepositoryInterface;
use App\Schema\Application\Command\ApplySchemaCommand;
use App\Schema\Domain\Service\SchemaDiffService;
use App\Schema\Domain\Service\SchemaSnapshotService;

final class ApplySchemaHandler
{
    public function __construct(
        private readonly SchemaSnapshotService             $snapshotService,
        private readonly SchemaDiffService                 $diffService,
        private readonly CreateCollectionHandler           $createCollectionHandler,
        private readonly UpdateCollectionHandler           $updateCollectionHandler,
        private readonly DeleteCollectionHandler           $deleteCollectionHandler,
        private readonly CollectionMetaRepositoryInterface $collectionRepository,
        private readonly CreateFieldHandler                $createFieldHandler,
        private readonly UpdateFieldHandler                $updateFieldHandler,
        private readonly DeleteFieldHandler                $deleteFieldHandler,
        private readonly FieldMetaRepositoryInterface      $fieldRepository,
        private readonly CreateRelationHandler             $createRelationHandler,
        private readonly DeleteRelationHandler             $deleteRelationHandler,
        private readonly RelationRepositoryInterface       $relationRepository,
    ) {}

    /** @return array{applied: array, skipped: array} */
    public function handle(ApplySchemaCommand $command): array
    {
        $current = $this->snapshotService->snapshot();
        $diff    = $this->diffService->diff($current, $command->snapshot);

        $applied = [];
        $skipped = [];

        // --- Collections ---
        foreach ($diff['collections']['create'] as $entry) {
            try {
                $this->createCollectionHandler->handle(new CreateCollectionCommand(
                    name:      $entry['collection'],
                    label:     $entry['meta']['label'] ?? null,
                    icon:      $entry['meta']['icon'] ?? null,
                    note:      $entry['meta']['note'] ?? null,
                    hidden:    $entry['meta']['hidden'] ?? false,
                    singleton: $entry['meta']['singleton'] ?? false,
                    sortField: $entry['meta']['sort_field'] ?? null,
                ));
                $applied[] = 'create_collection:' . $entry['collection'];
            } catch (CollectionAlreadyExistsException) {
                $skipped[] = 'create_collection:' . $entry['collection'];
            }
        }

        foreach ($diff['collections']['update'] as $entry) {
            $col = $this->collectionRepository->findByName($entry['collection']);
            if ($col === null) {
                $skipped[] = 'update_collection:' . $entry['collection'];
                continue;
            }
            $d = $entry['diff'];
            $this->updateCollectionHandler->handle(new UpdateCollectionCommand(
                name:      $entry['collection'],
                label:     $d['label']      ?? UpdateCollectionCommand::UNCHANGED,
                icon:      $d['icon']       ?? UpdateCollectionCommand::UNCHANGED,
                note:      $d['note']       ?? UpdateCollectionCommand::UNCHANGED,
                hidden:    $d['hidden']     ?? null,
                singleton: $d['singleton']  ?? null,
                sortField: $d['sort_field'] ?? UpdateCollectionCommand::UNCHANGED,
            ));
            $applied[] = 'update_collection:' . $entry['collection'];
        }

        if ($command->force) {
            foreach ($diff['collections']['delete'] as $name) {
                $col = $this->collectionRepository->findByName($name);
                if ($col !== null) {
                    $this->deleteCollectionHandler->handle(new DeleteCollectionCommand($name));
                    $applied[] = 'delete_collection:' . $name;
                }
            }
        }

        // --- Fields ---
        foreach ($diff['fields']['create'] as $entry) {
            try {
                $this->createFieldHandler->handle(new CreateFieldCommand(
                    collection: $entry['collection'],
                    field:      $entry['field'],
                    type:       $entry['type'],
                    label:      $entry['meta']['label'] ?? null,
                    note:       $entry['meta']['note'] ?? null,
                    required:   $entry['meta']['required'] ?? false,
                    readonly:   $entry['meta']['readonly'] ?? false,
                    hidden:     $entry['meta']['hidden'] ?? false,
                    sortOrder:  $entry['meta']['sort_order'] ?? 0,
                ));
                $applied[] = 'create_field:' . $entry['collection'] . '.' . $entry['field'];
            } catch (FieldAlreadyExistsException) {
                $skipped[] = 'create_field:' . $entry['collection'] . '.' . $entry['field'];
            }
        }

        foreach ($diff['fields']['update'] as $entry) {
            $f = $this->fieldRepository->findByCollectionAndField($entry['collection'], $entry['field']);
            if ($f === null) {
                $skipped[] = 'update_field:' . $entry['collection'] . '.' . $entry['field'];
                continue;
            }
            $d = $entry['diff'];
            $this->updateFieldHandler->handle(new UpdateFieldCommand(
                collection: $entry['collection'],
                field:      $entry['field'],
                label:      $d['meta']['label']      ?? UpdateFieldCommand::UNCHANGED,
                note:       $d['meta']['note']       ?? UpdateFieldCommand::UNCHANGED,
                required:   $d['meta']['required']   ?? null,
                readonly:   $d['meta']['readonly']   ?? null,
                hidden:     $d['meta']['hidden']     ?? null,
                sortOrder:  $d['meta']['sort_order'] ?? null,
            ));
            $applied[] = 'update_field:' . $entry['collection'] . '.' . $entry['field'];
        }

        if ($command->force) {
            foreach ($diff['fields']['delete'] as $entry) {
                $f = $this->fieldRepository->findByCollectionAndField($entry['collection'], $entry['field']);
                if ($f !== null) {
                    $this->deleteFieldHandler->handle(new DeleteFieldCommand($entry['collection'], $entry['field']));
                    $applied[] = 'delete_field:' . $entry['collection'] . '.' . $entry['field'];
                }
            }
        }

        // --- Relations ---
        foreach ($diff['relations']['create'] as $entry) {
            try {
                $this->createRelationHandler->handle(new CreateRelationCommand(
                    manyCollection:    $entry['many_collection'],
                    manyField:         $entry['many_field'],
                    oneCollection:     $entry['one_collection'] ?? null,
                    oneField:          $entry['one_field'] ?? null,
                    junctionCollection:$entry['junction_collection'] ?? null,
                ));
                $applied[] = 'create_relation:' . $entry['many_collection'] . '.' . $entry['many_field'];
            } catch (RelationAlreadyExistsException) {
                $skipped[] = 'create_relation:' . $entry['many_collection'] . '.' . $entry['many_field'];
            }
        }

        if ($command->force) {
            foreach ($diff['relations']['delete'] as $entry) {
                $r = $this->relationRepository->findByCollectionAndField($entry['many_collection'], $entry['many_field']);
                if ($r !== null) {
                    $this->deleteRelationHandler->handle(new DeleteRelationCommand(
                        $entry['many_collection'],
                        $entry['many_field'],
                    ));
                    $applied[] = 'delete_relation:' . $entry['many_collection'] . '.' . $entry['many_field'];
                }
            }
        }

        return ['applied' => $applied, 'skipped' => $skipped];
    }
}
