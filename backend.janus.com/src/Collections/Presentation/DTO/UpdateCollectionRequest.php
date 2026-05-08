<?php

/**
 * @file UpdateCollectionRequest.php
 *
 * Presentation-layer DTO for parsing a collection partial-update request body.
 *
 * @package App\Collections\Presentation\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Presentation\DTO;

use App\Collections\Application\Command\UpdateCollectionCommand;

/**
 * Parses the JSON body of a PATCH /collections/{name} request.
 *
 * Uses array_key_exists rather than isset for nullable fields so that an explicit
 * null in the payload is correctly distinguished from an absent key.
 */
final class UpdateCollectionRequest
{
    /**
     * Constructor
     *
     * @param string|null $label     New display label, or null (not distinguished from absent — label always nulls when omitted).
     * @param mixed       $icon      New icon identifier, null to clear, or UpdateCollectionCommand::UNCHANGED when absent.
     * @param mixed       $note      New administrative note, null to clear, or UpdateCollectionCommand::UNCHANGED when absent.
     * @param bool|null   $hidden    New visibility flag, or null when absent.
     * @param bool|null   $singleton New singleton flag, or null when absent.
     * @param mixed       $sortField New sort field name, null to clear, or UpdateCollectionCommand::UNCHANGED when absent.
     */
    public function __construct(
        public readonly ?string $label,
        public readonly mixed   $icon,
        public readonly mixed   $note,
        public readonly ?bool   $hidden,
        public readonly ?bool   $singleton,
        public readonly mixed   $sortField,
    ) {}

    /**
     * Parses the decoded JSON body into an UpdateCollectionRequest.
     *
     * @param  array<string, mixed> $data Decoded JSON body from the HTTP request.
     * @return self                        A parsed request object with sentinel values for absent fields.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            label:     isset($data['label'])      ? trim($data['label'])      : null,
            icon:      array_key_exists('icon', $data)       ? $data['icon']       : UpdateCollectionCommand::UNCHANGED,
            note:      array_key_exists('note', $data)       ? $data['note']       : UpdateCollectionCommand::UNCHANGED,
            hidden:    isset($data['hidden'])    ? (bool) $data['hidden']    : null,
            singleton: isset($data['singleton']) ? (bool) $data['singleton'] : null,
            sortField: array_key_exists('sort_field', $data) ? $data['sort_field'] : UpdateCollectionCommand::UNCHANGED,
        );
    }
}
