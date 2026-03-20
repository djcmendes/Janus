<?php

declare(strict_types=1);

namespace App\Translations\Application\Query\Handler;

use App\Translations\Application\DTO\TranslationDto;
use App\Translations\Application\Query\GetTranslationsQuery;
use App\Translations\Domain\Repository\TranslationRepositoryInterface;

final class GetTranslationsHandler
{
    public function __construct(private readonly TranslationRepositoryInterface $repository) {}

    /** @return array{data: TranslationDto[], total: int} */
    public function handle(GetTranslationsQuery $query): array
    {
        $translations = $this->repository->findPaginated($query->limit, $query->offset, $query->language, $query->key);
        $total        = $this->repository->countAll($query->language, $query->key);

        return [
            'data'  => array_map(TranslationDto::fromEntity(...), $translations),
            'total' => $total,
        ];
    }
}
