<?php

/**
 * @file ApplySchemaHandlerInterface.php
 *
 * Contract for the schema apply command handler.
 *
 * @package App\Schema\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Application\Command\Handler;

use App\Schema\Application\Command\ApplySchemaCommand;

/**
 * Handles ApplySchemaCommand and applies all schema diff operations.
 */
interface ApplySchemaHandlerInterface
{
    /**
     * @param  ApplySchemaCommand  $command The command carrying the target snapshot and force flag.
     * @return array<string, list<string>>  Keys: applied, skipped.
     */
    public function handle(ApplySchemaCommand $command): array;
}
