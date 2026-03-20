<?php

declare(strict_types=1);

namespace App\Users\Application\Command\Handler;

use App\Users\Application\Command\CreateUserCommand;
use App\Users\Application\DTO\UserDto;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Exception\UserAlreadyExistsException;
use App\Users\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class CreateUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface     $repository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function handle(CreateUserCommand $command): UserDto
    {
        if ($this->repository->findByEmail($command->email) !== null) {
            throw new UserAlreadyExistsException($command->email);
        }

        $user = new User($command->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $command->password));

        if ($command->firstName !== null) {
            $user->setFirstName($command->firstName);
        }
        if ($command->lastName !== null) {
            $user->setLastName($command->lastName);
        }
        if (!empty($command->roles)) {
            $user->setRoles($command->roles);
        }

        $this->repository->save($user);

        return UserDto::fromEntity($user);
    }
}
