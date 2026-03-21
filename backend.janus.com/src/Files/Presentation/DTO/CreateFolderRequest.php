<?php

declare(strict_types=1);

namespace App\Files\Presentation\DTO;

final class CreateFolderRequest
{
    public function __construct(
        public readonly string  $name,
        public readonly ?string $parentId = null,
    ) {}

    /** @throws \InvalidArgumentException */
    public static function fromArray(array $data): self
    {
        if (empty($data['name'])) {
            throw new \InvalidArgumentException('name is required.');
        }

        return new self(
            name:     trim($data['name']),
            parentId: $data['parent'] ?? null,
        );
    }
}
