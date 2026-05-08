<?php

/**
 * @file UpdateCollectionCommand.php
 *
 * CQRS command payload for partially updating an existing collection's metadata.
 *
 * @package App\Collections\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Command;

/**
 * Carries partial update data for a CMS collection.
 *
 * Fields that support explicit null (icon, note, sortField) use the UNCHANGED sentinel
 * to distinguish "caller explicitly sent null" from "caller did not include the field".
 * A null value means the field should be cleared; UNCHANGED means leave it as-is.
 */
final class UpdateCollectionCommand
{
    /**
     * Sentinel value indicating that a field was not included in the update payload
     * and must be left unchanged in the domain entity.
     *
     * @var string
     */
    public const UNCHANGED = '__UNCHANGED__';

    /**
     * Constructor
     *
     * @param string      $name      Collection name used to locate the record.
     * @param string|null $label     New display label, or null to clear (ignored if not provided).
     * @param mixed       $icon      New icon identifier, null to clear, or UNCHANGED to skip.
     * @param mixed       $note      New administrative note, null to clear, or UNCHANGED to skip.
     * @param bool|null   $hidden    New visibility flag, or null to skip.
     * @param bool|null   $singleton New singleton flag, or null to skip.
     * @param mixed       $sortField New sort field name, null to clear, or UNCHANGED to skip.
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $label     = null,
        public readonly mixed  $icon       = self::UNCHANGED,
        public readonly mixed  $note       = self::UNCHANGED,
        public readonly ?bool  $hidden     = null,
        public readonly ?bool  $singleton  = null,
        public readonly mixed  $sortField  = self::UNCHANGED,
    ) {}
}
