<?php

declare(strict_types=1);

namespace App\Shares\Application\Command\Handler;

use App\Shares\Application\Command\DeleteShareCommand;
use App\Shares\Domain\Exception\ShareForbiddenException;
use App\Shares\Domain\Exception\ShareNotFoundException;
use App\Shares\Domain\Repository\ShareRepositoryInterface;

final class DeleteShareHandler
{
    public function __construct(private readonly ShareRepositoryInterface $repository) {}

    public function handle(DeleteShareCommand $command): void
    {
        $share = $this->repository->findById($command->id);

        if ($share === null) {
            throw new ShareNotFoundException($command->id);
        }

        if (!$command->isAdmin && !$share->isOwnedBy($command->requestingUserId)) {
            throw new ShareForbiddenException();
        }

        $this->repository->delete($share);
    }
}
