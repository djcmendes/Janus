<?php

/**
 * @file UpdateDashboardCommand.php
 *
 * Command payload for partially updating an existing dashboard.
 *
 * @package App\Dashboards\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Command;

/**
 * Carries a partial update for a dashboard identified by its UUID.
 *
 * Fields set to the UNCHANGED sentinel are ignored by the handler so that
 * only explicitly provided values modify the persisted record.
 */
final class UpdateDashboardCommand
{
    /** Sentinel value indicating a field was not included in the update request. */
    public const UNCHANGED = '__UNCHANGED__';

    /**
     * @param string      $id   UUID of the dashboard to update.
     * @param string|null $name New display name, or UNCHANGED to leave it untouched.
     * @param string|null $icon New icon identifier (or null to clear), or UNCHANGED.
     * @param string|null $note New note text (or null to clear), or UNCHANGED.
     */
    public function __construct(
        public readonly string       $id,
        public readonly string|null  $name,
        public readonly string|null  $icon,
        public readonly string|null  $note,
    ) {}
}
