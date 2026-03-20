<?php

declare(strict_types=1);

namespace App\Users\Application\Command\Handler;

use App\Users\Application\Command\UpdateUserCommand;
use App\Users\Application\DTO\UserDto;
use App\Users\Domain\Exception\UserNotFoundException;
use App\Users\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UpdateUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface     $repository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function handle(UpdateUserCommand $command): UserDto
    {
        $user = $this->repository->findActiveById($command->id);

        if ($user === null) {
            throw new UserNotFoundException($command->id);
        }

        if ($command->firstName !== null) {
            $user->setFirstName($command->firstName);
        }
        if ($command->lastName !== null) {
            $user->setLastName($command->lastName);
        }
        if ($command->roles !== null) {
            $user->setRoles($command->roles);
        }
        if ($command->status !== null) {
            $user->setStatus($command->status);
        }
        if ($command->password !== null) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $command->password));
        }

        $this->repository->save($user);

        return UserDto::fromEntity($user);
    }
}
