<?php

/**
 * @file CreateDashboardCommand.php
 *
 * Command payload for creating a new dashboard.
 *
 * @package App\Dashboards\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Command;

/**
 * Carries the data required to create a new dashboard.
 */
final class CreateDashboardCommand
{
    /**
     * @param string      $name   Human-readable display name.
     * @param string|null $icon   Optional icon identifier.
     * @param string|null $note   Optional descriptive note.
     * @param string|null $userId Owner user UUID; null creates a shared/global dashboard.
     */
    public function __construct(
        public readonly string  $name,
        public readonly ?string $icon   = null,
        public readonly ?string $note   = null,
        public readonly ?string $userId = null,
    ) {}
}
