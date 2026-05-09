<?php

/**
 * @file CreateFieldRequest.php
 *
 * Presentation-layer request DTO for the POST /fields/{collection} endpoint.
 * Validates and normalises the incoming JSON body before it reaches the application layer.
 *
 * @package App\Fields\Presentation\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Presentation\DTO;

use App\Fields\Domain\Enum\FieldType;

/**
 * Parsed and validated payload for the create-field endpoint.
 *
 * Created via the static fromArray() factory which performs all validation and throws
 * \InvalidArgumentException on any constraint violation.
 */
final class CreateFieldRequest
{
    /**
     * @param string                   $field     Column name (validated format).
     * @param string                   $type      FieldType backing value (validated enum).
     * @param string|null              $label     Optional display label.
     * @param string|null              $note      Optional descriptive note.
     * @param bool                     $required  Required flag (default: false).
     * @param bool                     $readonly  Read-only flag (default: false).
     * @param bool                     $hidden    Hidden flag (default: false).
     * @param int                      $sortOrder Display order (default: 0).
     * @param string|null              $interface Admin UI component identifier.
     * @param array<string,mixed>|null $options   Admin UI component options.
     */
    public function __construct(
        public readonly string  $field,
        public readonly string  $type,
        public readonly ?string $label     = null,
        public readonly ?string $note      = null,
        public readonly bool    $required  = false,
        public readonly bool    $readonly  = false,
        public readonly bool    $hidden    = false,
        public readonly int     $sortOrder = 0,
        public readonly ?string $interface = null,
        public readonly ?array  $options   = null,
    ) {}

    /**
     * Parses and validates a raw JSON-decoded body array into a CreateFieldRequest.
     *
     * @param  array<string, mixed> $data Decoded request body.
     * @return self                        Validated request DTO.
     *
     * @throws \InvalidArgumentException When required fields are missing or values are invalid.
     */
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

        $interface = isset($data['interface']) ? trim($data['interface']) : null;
        $options   = isset($data['options']) && is_array($data['options']) ? $data['options'] : null;

        return new self(
            field:     $fieldName,
            type:      $typeValue,
            label:     isset($data['label']) ? trim($data['label']) : null,
            note:      isset($data['note'])  ? trim($data['note'])  : null,
            required:  (bool) ($data['required']  ?? false),
            readonly:  (bool) ($data['readonly']  ?? false),
            hidden:    (bool) ($data['hidden']    ?? false),
            sortOrder: (int)  ($data['sort']      ?? 0),
            interface: $interface !== '' ? $interface : null,
            options:   $options,
        );
    }
}
