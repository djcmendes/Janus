<?php

/**
 * @file DeleteFieldCommand.php
 *
 * CQRS command payload for removing a field metadata record and its database column.
 *
 * @package App\Fields\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Application\Command;

/**
 * Identifies the field to delete by collection and column name.
 */
final class DeleteFieldCommand
{
    /**
     * @param string $collection Collection name.
     * @param string $field      Column name to remove.
     */
    public function __construct(
        public readonly string $collection,
        public readonly string $field,
    ) {}
}
