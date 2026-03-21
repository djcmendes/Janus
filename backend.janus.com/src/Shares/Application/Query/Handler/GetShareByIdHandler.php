<?php

declare(strict_types=1);

namespace App\Shares\Application\Query\Handler;

use App\Shares\Application\DTO\ShareDto;
use App\Shares\Application\Query\GetShareByIdQuery;
use App\Shares\Domain\Exception\ShareNotFoundException;
use App\Shares\Domain\Repository\ShareRepositoryInterface;

final class GetShareByIdHandler
{
    public function __construct(private readonly ShareRepositoryInterface $repository) {}

    public function handle(GetShareByIdQuery $query): ShareDto
    {
        $share = $this->repository->findById($query->id);

        if ($share === null) {
            throw new ShareNotFoundException($query->id);
        }

        return ShareDto::fromEntity($share);
    }
}
