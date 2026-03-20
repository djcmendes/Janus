<?php

declare(strict_types=1);

namespace App\Extensions\Application\Query\Handler;

use App\Extensions\Application\DTO\ExtensionDto;
use App\Extensions\Application\Query\GetExtensionByIdQuery;
use App\Extensions\Domain\Exception\ExtensionNotFoundException;
use App\Extensions\Domain\Repository\ExtensionRepositoryInterface;

final class GetExtensionByIdHandler
{
    public function __construct(private readonly ExtensionRepositoryInterface $repository) {}

    public function handle(GetExtensionByIdQuery $query): ExtensionDto
    {
        $extension = $this->repository->findById($query->id);

        if ($extension === null) {
            throw new ExtensionNotFoundException($query->id);
        }

        return ExtensionDto::fromEntity($extension);
    }
}
