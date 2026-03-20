<?php

declare(strict_types=1);

namespace App\Shares\Application\Command\Handler;

use App\Shares\Application\Command\CreateShareCommand;
use App\Shares\Application\DTO\ShareDto;
use App\Shares\Domain\Entity\Share;
use App\Shares\Domain\Repository\ShareRepositoryInterface;
use App\Shares\Domain\Service\ShareTokenService;

final class CreateShareHandler
{
    public function __construct(
        private readonly ShareRepositoryInterface $repository,
        private readonly ShareTokenService        $tokenService,
    ) {}

    public function handle(CreateShareCommand $command): ShareDto
    {
        $expiresAt = $command->expiresAt !== null
            ? new \DateTimeImmutable($command->expiresAt)
            : null;

        $password = $command->password !== null
            ? password_hash($command->password, PASSWORD_BCRYPT)
            : null;

        $share = new Share(
            $command->collection,
            $command->item,
            $command->userId,
            $this->tokenService->generate(),
            $command->name,
            $password,
            $expiresAt,
            $command->maxUses,
        );

        $this->repository->save($share);

        return ShareDto::fromEntity($share);
    }
}
