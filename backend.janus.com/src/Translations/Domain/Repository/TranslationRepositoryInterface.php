<?php

declare(strict_types=1);

namespace App\Translations\Domain\Repository;

use App\Translations\Domain\Entity\Translation;

interface TranslationRepositoryInterface
{
    public function save(Translation $translation): void;

    public function delete(Translation $translation): void;

    public function findById(string $id): ?Translation;

    public function findByLanguageAndKey(string $language, string $key): ?Translation;

    /** @return Translation[] */
    public function findAll(
        int     $limit,
        int     $offset,
        ?string $language = null,
        ?string $key      = null,
    ): array;

    public function countAll(
        ?string $language = null,
        ?string $key      = null,
    ): int;
}
