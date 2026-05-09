<?php

/**
 * @file UpdateFieldRequest.php
 *
 * Presentation-layer request DTO for the PATCH /fields/{collection}/{field} endpoint.
 * Uses the UpdateFieldCommand::UNCHANGED sentinel to distinguish omitted from explicitly null.
 *
 * @package App\Fields\Presentation\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Presentation\DTO;

use App\Fields\Application\Command\UpdateFieldCommand;

/**
 * Parsed payload for the update-field endpoint.
 *
 * Fields absent from the request body are set to UpdateFieldCommand::UNCHANGED.
 * The application-layer handler skips any property that equals the sentinel.
 */
final class UpdateFieldRequest
{
    /**
     * @param mixed     $label     New label, null to clear, or UNCHANGED if omitted.
     * @param mixed     $note      New note, null to clear, or UNCHANGED if omitted.
     * @param bool|null $required  New required flag, or null if omitted.
     * @param bool|null $readonly  New read-only flag, or null if omitted.
     * @param bool|null $hidden    New hidden flag, or null if omitted.
     * @param int|null  $sortOrder New sort order, or null if omitted.
     * @param mixed     $interface New interface identifier, null to clear, or UNCHANGED if omitted.
     * @param mixed     $options   New options map, null to clear, or UNCHANGED if omitted.
     */
    public function __construct(
        public readonly mixed $label,
        public readonly mixed $note,
        public readonly ?bool $required,
        public readonly ?bool $readonly,
        public readonly ?bool $hidden,
        public readonly ?int  $sortOrder,
        public readonly mixed $interface,
        public readonly mixed $options,
    ) {}

    /**
     * Parses a raw JSON-decoded body into an UpdateFieldRequest.
     *
     * Uses array_key_exists to distinguish "key present with null value" from "key absent",
     * assigning the UNCHANGED sentinel for absent keys.
     *
     * @param  array<string, mixed> $data Decoded request body.
     * @return self                        Parsed request DTO.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            label:     array_key_exists('label', $data)     ? $data['label']     : UpdateFieldCommand::UNCHANGED,
            note:      array_key_exists('note', $data)      ? $data['note']      : UpdateFieldCommand::UNCHANGED,
            required:  isset($data['required'])  ? (bool) $data['required']  : null,
            readonly:  isset($data['readonly'])  ? (bool) $data['readonly']  : null,
            hidden:    isset($data['hidden'])    ? (bool) $data['hidden']    : null,
            sortOrder: isset($data['sort'])      ? (int)  $data['sort']      : null,
            interface: array_key_exists('interface', $data) ? $data['interface'] : UpdateFieldCommand::UNCHANGED,
            options:   array_key_exists('options', $data)   ? $data['options']   : UpdateFieldCommand::UNCHANGED,
        );
    }
}
