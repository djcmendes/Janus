<?php

/**
 * @file UpdateDashboardRequest.php
 *
 * Presentation-layer DTO for parsing dashboard update request bodies.
 *
 * @package App\Dashboards\Presentation\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Presentation\DTO;

use App\Dashboards\Application\Command\UpdateDashboardCommand;

/**
 * Parses the JSON body of a PATCH /dashboards/{id} request.
 *
 * Each field defaults to UpdateDashboardCommand::UNCHANGED so that omitted
 * keys leave the corresponding dashboard property untouched.
 */
final class UpdateDashboardRequest
{
    /**
     * @param string|null $name New dashboard name, or UNCHANGED sentinel.
     * @param string|null $icon New icon identifier or null, or UNCHANGED sentinel.
     * @param string|null $note New note text or null, or UNCHANGED sentinel.
     */
    public function __construct(
        public readonly string|null $name = UpdateDashboardCommand::UNCHANGED,
        public readonly string|null $icon = UpdateDashboardCommand::UNCHANGED,
        public readonly string|null $note = UpdateDashboardCommand::UNCHANGED,
    ) {}

    /**
     * Builds an UpdateDashboardRequest from a decoded JSON array.
     *
     * Only keys present in the body are applied; absent keys keep the UNCHANGED sentinel.
     *
     * @param  array<string, mixed> $data Decoded request body.
     * @return self                        Partially-populated DTO.
     *
     * @throws \InvalidArgumentException When a provided name is blank.
     */
    public static function fromArray(array $data): self
    {
        $name = UpdateDashboardCommand::UNCHANGED;
        $icon = UpdateDashboardCommand::UNCHANGED;
        $note = UpdateDashboardCommand::UNCHANGED;

        if (array_key_exists('name', $data)) {
            $name = trim((string) ($data['name'] ?? ''));
            if ($name === '') {
                throw new \InvalidArgumentException('name must not be blank if provided.');
            }
        }

        if (array_key_exists('icon', $data)) {
            $icon = ($data['icon'] !== null && $data['icon'] !== '') ? (string) $data['icon'] : null;
        }

        if (array_key_exists('note', $data)) {
            $note = ($data['note'] !== null && $data['note'] !== '') ? (string) $data['note'] : null;
        }

        return new self($name, $icon, $note);
    }
}
