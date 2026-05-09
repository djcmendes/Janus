<?php

/**
 * @file CreateDashboardRequest.php
 *
 * Presentation-layer DTO for parsing and validating dashboard creation request bodies.
 *
 * @package App\Dashboards\Presentation\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Presentation\DTO;

/**
 * Parses and validates the JSON body of a POST /dashboards request.
 */
final class CreateDashboardRequest
{
    /**
     * @param string      $name Human-readable dashboard name (required, max 255 chars).
     * @param string|null $icon Optional icon identifier.
     * @param string|null $note Optional descriptive note.
     */
    public function __construct(
        public readonly string  $name,
        public readonly ?string $icon = null,
        public readonly ?string $note = null,
    ) {}

    /**
     * Constructs a CreateDashboardRequest from a decoded JSON array.
     *
     * @param  array<string, mixed> $data Decoded request body.
     * @return self                        Validated DTO.
     *
     * @throws \InvalidArgumentException When required fields are missing or invalid.
     */
    public static function fromArray(array $data): self
    {
        $name = trim((string) ($data['name'] ?? ''));

        if ($name === '') {
            throw new \InvalidArgumentException('name is required.');
        }

        if (strlen($name) > 255) {
            throw new \InvalidArgumentException('name must not exceed 255 characters.');
        }

        $icon = (isset($data['icon']) && $data['icon'] !== null && $data['icon'] !== '')
            ? (string) $data['icon']
            : null;

        $note = (isset($data['note']) && $data['note'] !== null && $data['note'] !== '')
            ? (string) $data['note']
            : null;

        return new self($name, $icon, $note);
    }
}
