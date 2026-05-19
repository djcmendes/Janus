<?php

/**
 * @file ApplySchemaCommand.php
 *
 * Command payload for applying a schema snapshot to the live database.
 *
 * @package App\Schema\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Application\Command;

/**
 * Carries the target snapshot and the force flag for schema application.
 *
 * When force is false, only create/update operations are performed.
 * When force is true, collections, fields, and relations absent from the
 * snapshot are also deleted.
 */
final class ApplySchemaCommand
{
    public function __construct(
        /** Full snapshot array (version + collections + relations) */
        public readonly array $snapshot,
        /** When true, collections/fields/relations absent from the snapshot are deleted */
        public readonly bool  $force = false,
    ) {}
}
