<?php

declare(strict_types=1);

namespace App\Revisions\Application\Query\Handler;

use App\Revisions\Application\DTO\RevisionDto;
use App\Revisions\Application\Query\GetRevisionByIdQuery;
use App\Revisions\Domain\Exception\RevisionNotFoundException;
use App\Revisions\Domain\Repository\RevisionRepositoryInterface;

final class GetRevisionByIdHandler
{
    public function __construct(
        private readonly RevisionRepositoryInterface $repository,
    ) {}

    /** @throws RevisionNotFoundException */
    public function handle(GetRevisionByIdQuery $query): RevisionDto
    {
        $revision = $this->repository->findById($query->id);

        if ($revision === null) {
            throw new RevisionNotFoundException($query->id);
        }

        return RevisionDto::fromEntity($revision);
    }
}
