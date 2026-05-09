<?php

/**
 * @file UpdateExtensionCommand.php
 *
 * CQRS command payload for partially updating an existing Extension.
 *
 * @package App\Extensions\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Application\Command;

/**
 * Carries partial-update data for an existing Extension record.
 *
 * Fields set to the UNCHANGED sentinel are left as-is by the handler.
 */
final class UpdateExtensionCommand
{
    /** Sentinel value — field should not be modified. */
    public const UNCHANGED = '__UNCHANGED__';

    /**
     * @param string      $id      UUID of the extension to update.
     * @param bool|string $enabled New enabled state, or UNCHANGED to skip.
     * @param string|null $version New version string, or UNCHANGED to skip.
     * @param mixed       $meta    New meta array, null to clear, or UNCHANGED to skip.
     */
    public function __construct(
        public readonly string      $id,
        public readonly bool|string $enabled,
        public readonly string|null $version,
        public readonly mixed       $meta,
    ) {}
}
