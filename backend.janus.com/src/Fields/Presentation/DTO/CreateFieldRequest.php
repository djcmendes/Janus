<?php

declare(strict_types=1);

namespace App\Fields\Presentation\DTO;

use App\Fields\Domain\Enum\FieldType;

final class CreateFieldRequest
{
    public function __construct(
        public readonly string  $field,
        public readonly string  $type,
        public readonly ?string $label     = null,
        public readonly ?string $note      = null,
        public readonly bool    $required  = false,
        public readonly bool    $readonly  = false,
        public readonly bool    $hidden    = false,
        public readonly int     $sortOrder = 0,
    ) {}

    /** @throws \InvalidArgumentException */
    public static function fromArray(array $data): self
    {
        if (empty($data['field'])) {
            throw new \InvalidArgumentException('field is required.');
        }

        $fieldName = trim($data['field']);
        if (!preg_match('/^[a-z][a-z0-9_]{0,63}$/i', $fieldName)) {
            throw new \InvalidArgumentException(
                'field must start with a letter and contain only letters, digits, or underscores (max 64 chars).'
            );
        }

        if (empty($data['type'])) {
            throw new \InvalidArgumentException('type is required.');
        }

        $typeValue = trim($data['type']);
        if (FieldType::tryFrom($typeValue) === null) {
            $valid = implode(', ', array_column(FieldType::cases(), 'value'));
            throw new \InvalidArgumentException(sprintf('Invalid type "%s". Valid values: %s.', $typeValue, $valid));
        }

        return new self(
            field:     $fieldName,
            type:      $typeValue,
            label:     isset($data['label']) ? trim($data['label']) : null,
            note:      isset($data['note'])  ? trim($data['note'])  : null,
            required:  (bool) ($data['required']  ?? false),
            readonly:  (bool) ($data['readonly']  ?? false),
            hidden:    (bool) ($data['hidden']    ?? false),
            sortOrder: (int)  ($data['sort']      ?? 0),
        );
    }
}
