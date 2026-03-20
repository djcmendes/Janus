<?php

declare(strict_types=1);

namespace App\Collections\Presentation\DTO;

final class CreateCollectionRequest
{
    public function __construct(
        public readonly string  $name,
        public readonly ?string $label     = null,
        public readonly ?string $icon      = null,
        public readonly ?string $note      = null,
        public readonly bool    $hidden    = false,
        public readonly bool    $singleton = false,
        public readonly ?string $sortField = null,
    ) {}

    /** @throws \InvalidArgumentException */
    public static function fromArray(array $data): self
    {
        if (empty($data['name'])) {
            throw new \InvalidArgumentException('name is required.');
        }

        $name = trim($data['name']);
        if (!preg_match('/^[a-z][a-z0-9_]{0,63}$/i', $name)) {
            throw new \InvalidArgumentException(
                'name must start with a letter and contain only letters, digits, or underscores (max 64 chars).'
            );
        }

        return new self(
            name:      $name,
            label:     isset($data['label'])      ? trim($data['label'])      : null,
            icon:      isset($data['icon'])        ? trim($data['icon'])       : null,
            note:      isset($data['note'])        ? trim($data['note'])       : null,
            hidden:    (bool) ($data['hidden']    ?? false),
            singleton: (bool) ($data['singleton'] ?? false),
            sortField: isset($data['sort_field']) ? trim($data['sort_field']) : null,
        );
    }
}
