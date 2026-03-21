<?php

declare(strict_types=1);

namespace App\Collections\Presentation\DTO;

use App\Collections\Application\Command\UpdateCollectionCommand;

final class UpdateCollectionRequest
{
    public function __construct(
        public readonly ?string $label,
        public readonly mixed   $icon,
        public readonly mixed   $note,
        public readonly ?bool   $hidden,
        public readonly ?bool   $singleton,
        public readonly mixed   $sortField,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            label:     isset($data['label'])      ? trim($data['label'])      : null,
            icon:      array_key_exists('icon', $data)       ? $data['icon']       : UpdateCollectionCommand::UNCHANGED,
            note:      array_key_exists('note', $data)       ? $data['note']       : UpdateCollectionCommand::UNCHANGED,
            hidden:    isset($data['hidden'])    ? (bool) $data['hidden']    : null,
            singleton: isset($data['singleton']) ? (bool) $data['singleton'] : null,
            sortField: array_key_exists('sort_field', $data) ? $data['sort_field'] : UpdateCollectionCommand::UNCHANGED,
        );
    }
}
