<?php

declare(strict_types=1);

namespace App\Files\Application\Query\Handler;

use App\Files\Application\DTO\FileDto;
use App\Files\Application\Query\GetFileByIdQuery;
use App\Files\Domain\Exception\FileNotFoundException;
use App\Files\Domain\Repository\FileRepositoryInterface;

final class GetFileByIdHandler
{
    public function __construct(private readonly FileRepositoryInterface $repository) {}

    /** @throws FileNotFoundException */
    public function handle(GetFileByIdQuery $query): FileDto
    {
        $file = $this->repository->findById($query->id);

        if ($file === null) {
            throw new FileNotFoundException($query->id);
        }

        return FileDto::fromEntity($file);
    }
}
