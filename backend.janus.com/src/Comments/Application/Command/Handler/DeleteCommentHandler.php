<?php

/**
 * @file DeleteCommentHandler.php
 *
 * Application handler for the delete-comment command.
 *
 * @package App\Comments\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Command\Handler;

use App\Comments\Application\Command\DeleteCommentCommand;
use App\Comments\Domain\Exception\CommentForbiddenException;
use App\Comments\Domain\Exception\CommentNotFoundException;
use App\Comments\Domain\Repository\CommentRepositoryInterface;

/**
 * Deletes an existing Comment, enforcing ownership unless the caller is an admin.
 */
final class DeleteCommentHandler
{
    /**
     * @param CommentRepositoryInterface $repository Persists and retrieves Comment domain entities.
     */
    public function __construct(
        private readonly CommentRepositoryInterface $repository,
    ) {}

    /**
     * Loads the comment, verifies ownership, and removes it from the repository.
     *
     * @param  DeleteCommentCommand  $command The command carrying the id, requesting user, and admin flag.
     * @return void
     *
     * @throws CommentNotFoundException   When no comment exists for the given id.
     * @throws CommentForbiddenException  When the requesting user neither owns nor has admin rights over the comment.
     */
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
