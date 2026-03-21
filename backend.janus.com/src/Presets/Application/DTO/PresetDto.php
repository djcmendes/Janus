<?php

declare(strict_types=1);

namespace App\Presets\Application\DTO;

use App\Presets\Domain\Entity\Preset;

final class PresetDto
{
    public function __construct(
        public readonly string  $id,
        public readonly ?string $collection,
        public readonly ?string $layout,
        public readonly ?array  $layoutOptions,
        public readonly ?array  $layoutQuery,
        public readonly ?array  $filter,
        public readonly ?string $search,
        public readonly ?string $bookmark,
        public readonly ?string $userId,
        public readonly string  $createdAt,
        public readonly string  $updatedAt,
    ) {}

    public static function fromEntity(Preset $preset): self
    {
        return new self(
            id:            $preset->getId(),
            collection:    $preset->getCollection(),
            layout:        $preset->getLayout(),
            layoutOptions: $preset->getLayoutOptions(),
            layoutQuery:   $preset->getLayoutQuery(),
            filter:        $preset->getFilter(),
            search:        $preset->getSearch(),
            bookmark:      $preset->getBookmark(),
            userId:        $preset->getUserId(),
            createdAt:     $preset->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:     $preset->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
