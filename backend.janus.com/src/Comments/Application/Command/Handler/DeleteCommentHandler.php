<?php

declare(strict_types=1);

namespace App\Comments\Application\Command\Handler;

use App\Comments\Application\Command\DeleteCommentCommand;
use App\Comments\Domain\Exception\CommentForbiddenException;
use App\Comments\Domain\Exception\CommentNotFoundException;
use App\Comments\Domain\Repository\CommentRepositoryInterface;

final class DeleteCommentHandler
{
    public function __construct(
        private readonly CommentRepositoryInterface $repository,
    ) {}

    public function handle(DeleteCommentCommand $command): void
    {
        $comment = $this->repository->findById($command->id);

        if ($comment === null) {
            throw new CommentNotFoundException($command->id);
        }

        if (!$command->isAdmin && !$comment->isOwnedBy($command->requestingUserId)) {
            throw new CommentForbiddenException();
        }

        $this->repository->delete($comment);
    }
}
