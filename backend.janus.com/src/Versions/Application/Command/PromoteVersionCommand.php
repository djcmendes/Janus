<?php

/**
 * @file PromoteVersionCommand.php
 *
 * Write-side payload for promoting a Version's data back into the live item row.
 *
 * @package App\Versions\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command;

/**
 * Carries the UUID of the Version whose data snapshot should be restored to the live item.
 */
final class PromoteVersionCommand
{
    /**
     * @param string $id UUID of the version to promote.
     */
    public function __construct(public readonly string $id) {}
}
