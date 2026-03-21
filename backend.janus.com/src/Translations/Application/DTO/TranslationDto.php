<?php

declare(strict_types=1);

namespace App\Translations\Application\DTO;

use App\Translations\Domain\Entity\Translation;

final class TranslationDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $language,
        public readonly string $key,
        public readonly string $value,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}

    public static function fromEntity(Translation $translation): self
    {
        return new self(
            id:        $translation->getId(),
            language:  $translation->getLanguage(),
            key:       $translation->getKey(),
            value:     $translation->getValue(),
            createdAt: $translation->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $translation->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
