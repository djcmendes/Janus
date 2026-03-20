<?php

declare(strict_types=1);

namespace App\Revisions\Application\Query\Handler;

use App\Revisions\Application\DTO\RevisionDto;
use App\Revisions\Application\Query\GetRevisionsQuery;
use App\Revisions\Domain\Repository\RevisionRepositoryInterface;

final class GetRevisionsHandler
{
    public function __construct(
        private readonly RevisionRepositoryInterface $repository,
    ) {}

    /** @return array{data: RevisionDto[], total: int} */
    public function handle(GetRevisionsQuery $query): array
    {
        $revisions = $this->repository->findAll(
            $query->limit,
            $query->offset,
            $query->collection,
            $query->item,
        );

        $total = $this->repository->countAll(
            $query->collection,
            $query->item,
        );

        return [
            'data'  => array_map(RevisionDto::fromEntity(...), $revisions),
            'total' => $total,
        ];
    }
}
