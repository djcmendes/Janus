<?php

/**
 * @file UpdateCommentHandler.php
 *
 * Application handler for the update-comment command.
 *
 * @package App\Comments\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Command\Handler;

use App\Comments\Application\Command\UpdateCommentCommand;
use App\Comments\Application\DTO\CommentDto;
use App\Comments\Domain\Exception\CommentForbiddenException;
use App\Comments\Domain\Exception\CommentNotFoundException;
use App\Comments\Domain\Repository\CommentRepositoryInterface;

/**
 * Updates the text of an existing Comment, enforcing ownership unless the caller is an admin.
 */
final class UpdateCommentHandler
{
    /**
     * @param CommentRepositoryInterface $repository Persists and retrieves Comment domain entities.
     */
    public function __construct(
        private readonly CommentRepositoryInterface $repository,
    ) {}

    /**
     * Loads the comment, verifies ownership, updates the text, and persists the change.
     *
     * @param  UpdateCommentCommand  $command The command carrying the id, new text, requesting user, and admin flag.
     * @return CommentDto                     DTO representation of the updated comment.
     *
     * @throws CommentNotFoundException   When no comment exists for the given id.
     * @throws CommentForbiddenException  When the requesting user neither owns nor has admin rights over the comment.
     */
    public function handle(UpdateCommentCommand $command): CommentDto
    {
        $comment = $this->repository->findById($command->id);

        if ($comment === null) {
            throw new CommentNotFoundException($command->id);
        }

        if (!$command->isAdmin && !$comment->isOwnedBy($command->requestingUserId)) {
            throw new CommentForbiddenException();
        }

        $comment->setComment($command->comment);

        $this->repository->save($comment);

        return CommentDto::fromEntity($comment);
    }
}
