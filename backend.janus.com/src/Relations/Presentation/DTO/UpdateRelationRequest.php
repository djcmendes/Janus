<?php

declare(strict_types=1);

namespace App\Relations\Presentation\DTO;

use App\Relations\Application\Command\UpdateRelationCommand;

final class UpdateRelationRequest
{
    public function __construct(
        public readonly mixed $oneCollection,
        public readonly mixed $oneField,
        public readonly mixed $junctionCollection,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            oneCollection:      array_key_exists('one_collection', $data)      ? $data['one_collection']      : UpdateRelationCommand::UNCHANGED,
            oneField:           array_key_exists('one_field', $data)           ? $data['one_field']           : UpdateRelationCommand::UNCHANGED,
            junctionCollection: array_key_exists('junction_collection', $data) ? $data['junction_collection'] : UpdateRelationCommand::UNCHANGED,
        );
    }
}
