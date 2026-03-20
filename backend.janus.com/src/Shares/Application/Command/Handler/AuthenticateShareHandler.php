<?php

declare(strict_types=1);

namespace App\Shares\Application\Command\Handler;

use App\Shares\Application\Command\AuthenticateShareCommand;
use App\Shares\Application\DTO\ShareDto;
use App\Shares\Domain\Exception\ShareInvalidException;
use App\Shares\Domain\Exception\ShareNotFoundException;
use App\Shares\Domain\Repository\ShareRepositoryInterface;

final class AuthenticateShareHandler
{
    public function __construct(private readonly ShareRepositoryInterface $repository) {}

    public function handle(AuthenticateShareCommand $command): ShareDto
    {
        $share = $this->repository->findByToken($command->token);

        if ($share === null) {
            throw new ShareNotFoundException($command->token);
        }

        if (!$share->isValid()) {
            throw new ShareInvalidException();
        }

        if ($share->getPassword() !== null) {
            if ($command->password === null || !password_verify($command->password, $share->getPassword())) {
                throw new ShareInvalidException('Invalid password for this share.');
            }
        }

        $share->recordUse();
        $this->repository->save($share);

        return ShareDto::fromEntity($share);
    }
}
