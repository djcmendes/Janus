<?php

/**
 * @file UpdateVersionCommand.php
 *
 * Write-side payload for mutating the mutable fields of an existing Version.
 *
 * @package App\Versions\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command;

/**
 * Carries the fields that may be changed on an existing Version record.
 *
 * Fields left at the UNCHANGED sentinel are not written, allowing partial updates
 * without requiring the caller to know or re-send the current values.
 */
final class UpdateVersionCommand
{
    /** @var string Sentinel value indicating a field should not be modified. */
    public const UNCHANGED = '__UNCHANGED__';

    /**
     * @param string $id    UUID of the version record to update.
     * @param mixed  $key   New version label, or UNCHANGED to leave as-is.
     * @param mixed  $delta New diff against the previous version, or UNCHANGED to leave as-is.
     */
    public function __construct(
        public readonly string $id,
        public readonly mixed  $key   = self::UNCHANGED,
        public readonly mixed  $delta = self::UNCHANGED,
    ) {}
}
