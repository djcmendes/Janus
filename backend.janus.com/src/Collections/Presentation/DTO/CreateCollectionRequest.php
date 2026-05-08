<?php

/**
 * @file CreateCollectionRequest.php
 *
 * Presentation-layer DTO for validating and parsing a collection creation request body.
 *
 * @package App\Collections\Presentation\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Presentation\DTO;

/**
 * Parses and validates the JSON body of a POST /collections request.
 *
 * Validates the name format (starts with letter, alphanumeric/underscores, max 64 chars)
 * and sets sensible defaults for optional fields. Throws \InvalidArgumentException when
 * required fields are missing or when the name format is invalid.
 */
final class CreateCollectionRequest
{
    /**
     * Constructor
     *
     * @param string      $name            Database table name and collection route handle.
     * @param string|null $label           Human-readable display label, or null.
     * @param string|null $icon            Icon identifier, or null.
     * @param string|null $note            Administrative note, or null.
     * @param bool        $hidden          Whether the collection is hidden from navigation.
     * @param bool        $singleton       Whether the collection is restricted to a single record.
     * @param string|null $sortField       Field name for manual sorting, or null.
     * @param string      $primaryKeyField Column name for the auto-created primary key (default: 'id').
     * @param string      $primaryKeyType  Type of the primary key: uuid | integer | bigInteger | string (default: 'uuid').
     */
    public function __construct(
        public readonly string  $name,
        public readonly ?string $label          = null,
        public readonly ?string $icon           = null,
        public readonly ?string $note           = null,
        public readonly bool    $hidden         = false,
        public readonly bool    $singleton      = false,
        public readonly ?string $sortField      = null,
        public readonly string  $primaryKeyField = 'id',
        public readonly string  $primaryKeyType  = 'uuid',
    ) {}

    /**
     * Parses and validates the decoded JSON body into a CreateCollectionRequest.
     *
     * @param  array<string, mixed>    $data Decoded JSON body from the HTTP request.
     * @return self                          A validated request object.
     *
     * @throws \InvalidArgumentException When the name is missing or fails format validation.
     */
    public static function fromArray(array $data): self
    {
        if (empty($data['name'])) {
            throw new \InvalidArgumentException('name is required.');
        }

        $name = trim($data['name']);
        if (!preg_match('/^[a-z][a-z0-9_]{0,63}$/i', $name)) {
            throw new \InvalidArgumentException(
                'name must start with a letter and contain only letters, digits, or underscores (max 64 chars).'
            );
        }

        $primaryKeyField = trim($data['primary_key_field'] ?? 'id') ?: 'id';
        $primaryKeyType  = trim($data['primary_key_type']  ?? 'uuid') ?: 'uuid';

        return new self(
            name:            $name,
            label:           isset($data['label'])      ? trim($data['label'])      : null,
            icon:            isset($data['icon'])        ? trim($data['icon'])       : null,
            note:            isset($data['note'])        ? trim($data['note'])       : null,
            hidden:          (bool) ($data['hidden']    ?? false),
            singleton:       (bool) ($data['singleton'] ?? false),
            sortField:       isset($data['sort_field']) ? trim($data['sort_field']) : null,
            primaryKeyField: $primaryKeyField,
            primaryKeyType:  $primaryKeyType,
        );
    }
}
