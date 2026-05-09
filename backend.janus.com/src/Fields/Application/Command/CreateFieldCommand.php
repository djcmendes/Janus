<?php

/**
 * @file CreateFieldCommand.php
 *
 * CQRS command payload for creating a new field metadata record.
 *
 * @package App\Fields\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Application\Command;

/**
 * Carries all data required to create a new field within a collection.
 */
final class CreateFieldCommand
{
    /**
     * @param string                   $collection Collection name.
     * @param string                   $field      Column name within the collection.
     * @param string                   $type       FieldType backing value (validated by handler).
     * @param string|null              $label      Optional display label.
     * @param string|null              $note       Optional descriptive note.
     * @param bool                     $required   Whether the field is required.
     * @param bool                     $readonly   Whether the field is read-only in Admin UI.
     * @param bool                     $hidden     Whether the field is hidden in Admin UI.
     * @param int                      $sortOrder  Display order.
     * @param string|null              $interface  Admin UI component identifier.
     * @param array<string,mixed>|null $options    Admin UI component options.
     */
    public function __construct(
        public readonly string  $collection,
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
}
