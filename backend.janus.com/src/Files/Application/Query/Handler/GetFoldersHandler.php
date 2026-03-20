<?php

declare(strict_types=1);

namespace App\Files\Application\Query\Handler;

use App\Files\Application\DTO\FolderDto;
use App\Files\Application\Query\GetFoldersQuery;
use App\Files\Domain\Repository\FolderRepositoryInterface;

final class GetFoldersHandler
{
    public function __construct(private readonly FolderRepositoryInterface $repository) {}

    /** @return array{data: FolderDto[], total: int} */
    public function handle(GetFoldersQuery $query): array
    {
        $folders = $this->repository->findAll($query->limit, $query->offset);
        $total   = $this->repository->count();

        return [
            'data'  => array_map(FolderDto::fromEntity(...), $folders),
            'total' => $total,
        ];
    }
}
