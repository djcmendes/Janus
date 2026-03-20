<?php

declare(strict_types=1);

namespace App\Users\Application\Query\Handler;

use App\Users\Application\DTO\UserDto;
use App\Users\Application\Query\GetUsersQuery;
use App\Users\Domain\Repository\UserRepositoryInterface;

final class GetUsersHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
    ) {}

    /** @return array{data: UserDto[], total: int} */
    public function handle(GetUsersQuery $query): array
    {
        $users = $this->repository->findAllActive($query->limit, $query->offset);
        $total = $this->repository->countActive();

        return [
            'data'  => array_map(UserDto::fromEntity(...), $users),
            'total' => $total,
        ];
    }
}
