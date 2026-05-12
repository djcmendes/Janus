<?php

/**
 * @file DeleteVersionCommand.php
 *
 * Write-side payload for deleting a Version record.
 *
 * @package App\Versions\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command;

/**
 * Carries the UUID of the Version to be permanently removed.
 */
final class DeleteVersionCommand
{
    /**
     * @param string $id UUID of the version record to delete.
     */
    public function __construct(public readonly string $id) {}
}
