<?php

/**
 * @file CreateCollectionCommand.php
 *
 * CQRS command payload for creating a new collection.
 *
 * @package App\Collections\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Command;

/**
 * Carries all data needed to create a new CMS collection with its first primary-key field.
 */
final class CreateCollectionCommand
{
    /**
     * Constructor
     *
     * @param string      $name            Database table name and collection route handle (max 64 chars).
     * @param string|null $label           Human-readable display label, or null.
     * @param string|null $icon            Icon identifier, or null.
     * @param string|null $note            Administrative note, or null.
     * @param bool        $hidden          Whether the collection is hidden from navigation (default: false).
     * @param bool        $singleton       Whether the collection is restricted to a single record (default: false).
     * @param string|null $sortField       Field name for manual drag-and-drop sorting, or null.
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
}
