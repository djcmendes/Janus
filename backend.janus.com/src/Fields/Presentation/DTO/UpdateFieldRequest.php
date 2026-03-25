<?php

declare(strict_types=1);

namespace App\Fields\Presentation\DTO;

use App\Fields\Application\Command\UpdateFieldCommand;

final class UpdateFieldRequest
{
    public function __construct(
        public readonly mixed $label,
        public readonly mixed $note,
        public readonly ?bool $required,
        public readonly ?bool $readonly,
        public readonly ?bool $hidden,
        public readonly ?int  $sortOrder,
        public readonly mixed $interface,
        public readonly mixed $options,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            label:     array_key_exists('label', $data)     ? $data['label']     : UpdateFieldCommand::UNCHANGED,
            note:      array_key_exists('note', $data)      ? $data['note']      : UpdateFieldCommand::UNCHANGED,
            required:  isset($data['required'])  ? (bool) $data['required']  : null,
            readonly:  isset($data['readonly'])  ? (bool) $data['readonly']  : null,
            hidden:    isset($data['hidden'])    ? (bool) $data['hidden']    : null,
            sortOrder: isset($data['sort'])      ? (int)  $data['sort']      : null,
            interface: array_key_exists('interface', $data) ? $data['interface'] : UpdateFieldCommand::UNCHANGED,
            options:   array_key_exists('options', $data)   ? $data['options']   : UpdateFieldCommand::UNCHANGED,
        );
    }
}
