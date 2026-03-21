<?php

declare(strict_types=1);

namespace App\Users\Application\Command\Handler;

use App\Users\Application\Command\DeleteUserCommand;
use App\Users\Domain\Exception\UserNotFoundException;
use App\Users\Domain\Repository\UserRepositoryInterface;

final class DeleteUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
    ) {}

    public function handle(DeleteUserCommand $command): void
    {
        $user = $this->repository->findActiveById($command->id);

        if ($user === null) {
            throw new UserNotFoundException($command->id);
        }

        $user->softDelete();
        $this->repository->save($user);
    }
}
