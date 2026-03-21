<?php

declare(strict_types=1);

namespace App\Users\Application\Command\Handler;

use App\Users\Application\Command\InviteUserCommand;
use App\Users\Application\DTO\UserDto;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Exception\UserAlreadyExistsException;
use App\Users\Domain\Message\InviteEmailMessage;
use App\Users\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class InviteUserHandler
{
    private const TOKEN_TTL_HOURS = 48;

    public function __construct(
        private readonly UserRepositoryInterface $repository,
        private readonly MessageBusInterface     $bus,
        private readonly string                  $appBaseUrl,
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

        $this->bus->dispatch(new InviteEmailMessage(
            recipientEmail: $command->email,
            inviteToken:    $token,
            appBaseUrl:     $this->appBaseUrl,
        ));

        return UserDto::fromEntity($user);
    }
}
