<?php

declare(strict_types=1);

namespace App\Users\Application\Command\Handler;

use App\Users\Application\Command\InviteUserCommand;
use App\Users\Application\DTO\UserDto;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Exception\UserAlreadyExistsException;
use App\Users\Domain\Repository\UserRepositoryInterface;

final class InviteUserHandler
{
    private const TOKEN_TTL_HOURS = 48;

    public function __construct(
        private readonly UserRepositoryInterface $repository,
    ) {}

    public function handle(InviteUserCommand $command): UserDto
    {
        if ($this->repository->findByEmail($command->email) !== null) {
            throw new UserAlreadyExistsException($command->email);
        }

        $user = new User($command->email);
        $user->setStatus('invited');
        $user->setPassword(''); // No password until invite is accepted

        if ($command->firstName !== null) {
            $user->setFirstName($command->firstName);
        }
        if ($command->lastName !== null) {
            $user->setLastName($command->lastName);
        }
        if (!empty($command->roles)) {
            $user->setRoles($command->roles);
        }

        $token     = bin2hex(random_bytes(32));
        $expiresAt = new \DateTimeImmutable(sprintf('+%d hours', self::TOKEN_TTL_HOURS));
        $user->setInviteToken($token, $expiresAt);

        $this->repository->save($user);

        // TODO: dispatch InviteEmailMessage via Symfony Messenger when mailer is integrated

        return UserDto::fromEntity($user);
    }
}
