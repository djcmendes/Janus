<?php

declare(strict_types=1);

namespace App\Users\Application\Query\Handler;

use App\Users\Application\DTO\UserDto;
use App\Users\Application\Query\GetUserByIdQuery;
use App\Users\Domain\Exception\UserNotFoundException;
use App\Users\Domain\Repository\UserRepositoryInterface;

final class GetUserByIdHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
    ) {}

    public function handle(GetUserByIdQuery $query): UserDto
    {
        $user = $this->repository->findActiveById($query->id);

        if ($user === null) {
            throw new UserNotFoundException($query->id);
        }

        return UserDto::fromEntity($user);
    }
}
