<?php

/**
 * @file DeleteExtensionCommand.php
 *
 * CQRS command payload for removing an Extension from the registry.
 *
 * @package App\Extensions\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Application\Command;

/**
 * Carries the UUID of the extension to delete.
 */
final class DeleteExtensionCommand
{
    /**
     * @param string $id UUID of the extension to remove.
     */
    public function __construct(public readonly string $id) {}
}
