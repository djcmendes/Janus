<?php

declare(strict_types=1);

namespace App\Files\Application\Query\Handler;

use App\Files\Application\DTO\FolderDto;
use App\Files\Application\Query\GetFolderByIdQuery;
use App\Files\Domain\Exception\FolderNotFoundException;
use App\Files\Domain\Repository\FolderRepositoryInterface;

final class GetFolderByIdHandler
{
    public function __construct(private readonly FolderRepositoryInterface $repository) {}

    /** @throws FolderNotFoundException */
    public function handle(GetFolderByIdQuery $query): FolderDto
    {
        $folder = $this->repository->findById($query->id);

        if ($folder === null) {
            throw new FolderNotFoundException($query->id);
        }

        return FolderDto::fromEntity($folder);
    }
}
