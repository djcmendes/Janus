<?php

declare(strict_types=1);

namespace App\Files\Application\Query\Handler;

use App\Files\Application\DTO\FileDto;
use App\Files\Application\Query\GetFilesQuery;
use App\Files\Domain\Repository\FileRepositoryInterface;

final class GetFilesHandler
{
    public function __construct(private readonly FileRepositoryInterface $repository) {}

    /** @return array{data: FileDto[], total: int} */
    public function handle(GetFilesQuery $query): array
    {
        $files = $this->repository->findPaginated($query->limit, $query->offset, $query->folderId);
        $total = $this->repository->countAll($query->folderId);

        return [
            'data'  => array_map(FileDto::fromEntity(...), $files),
            'total' => $total,
        ];
    }
}
