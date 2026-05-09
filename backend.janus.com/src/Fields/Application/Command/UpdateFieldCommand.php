<?php

/**
 * @file UpdateFieldCommand.php
 *
 * CQRS command payload for partially updating a field metadata record.
 * Uses a sentinel value to distinguish "omitted" from "explicitly null".
 *
 * @package App\Fields\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Application\Command;

/**
 * Carries partial update data for an existing FieldMeta record.
 *
 * Fields that should not be mutated are set to the UNCHANGED sentinel.
 * The handler checks each field against the sentinel and skips it when matched.
 * Boolean and integer fields use null to indicate "no change".
 */
final class UpdateFieldCommand
{
    /** @var string Sentinel value indicating a field should not be mutated. */
    public const UNCHANGED = '__UNCHANGED__';

    /**
     * @param string $collection Collection name (identifies the target record).
     * @param string $field      Column name (identifies the target record).
     * @param mixed  $label      New label, null to clear, or UNCHANGED to skip.
     * @param mixed  $note       New note, null to clear, or UNCHANGED to skip.
     * @param bool|null  $required   New required flag, or null to skip.
     * @param bool|null  $readonly   New read-only flag, or null to skip.
     * @param bool|null  $hidden     New hidden flag, or null to skip.
     * @param int|null   $sortOrder  New sort order, or null to skip.
     * @param mixed  $interface  New interface ID, null to clear, or UNCHANGED to skip.
     * @param mixed  $options    New options map, null to clear, or UNCHANGED to skip.
     */
    public function __construct(
        public readonly string  $collection,
        public readonly string  $field,
        public readonly mixed   $label     = self::UNCHANGED,
        public readonly mixed   $note      = self::UNCHANGED,
        public readonly ?bool   $required  = null,
        public readonly ?bool   $readonly  = null,
        public readonly ?bool   $hidden    = null,
        public readonly ?int    $sortOrder = null,
        public readonly mixed   $interface = self::UNCHANGED,
        public readonly mixed   $options   = self::UNCHANGED,
    ) {}
}
