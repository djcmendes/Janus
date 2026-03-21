<?php

declare(strict_types=1);

namespace App\Files\Presentation\DTO;

use App\Files\Application\Command\UpdateFolderCommand;

final class UpdateFolderRequest
{
    public function __construct(
        public readonly ?string $name,
        public readonly mixed   $parentId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:     isset($data['name']) ? trim($data['name']) : null,
            parentId: array_key_exists('parent', $data) ? $data['parent'] : UpdateFolderCommand::UNCHANGED,
        );
    }
}
