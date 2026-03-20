<?php

declare(strict_types=1);

namespace App\Translations\Application\Query\Handler;

use App\Translations\Application\DTO\TranslationDto;
use App\Translations\Application\Query\GetTranslationByIdQuery;
use App\Translations\Domain\Exception\TranslationNotFoundException;
use App\Translations\Domain\Repository\TranslationRepositoryInterface;

final class GetTranslationByIdHandler
{
    public function __construct(private readonly TranslationRepositoryInterface $repository) {}

    public function handle(GetTranslationByIdQuery $query): TranslationDto
    {
        $translation = $this->repository->findById($query->id);

        if ($translation === null) {
            throw new TranslationNotFoundException($query->id);
        }

        return TranslationDto::fromEntity($translation);
    }
}
