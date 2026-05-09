<?php

/**
 * @file CreateCommentHandler.php
 *
 * Application handler for the create-comment command.
 *
 * @package App\Comments\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Command\Handler;

use App\Comments\Application\Command\CreateCommentCommand;
use App\Comments\Application\DTO\CommentDto;
use App\Comments\Domain\Entity\Comment;
use App\Comments\Domain\Repository\CommentRepositoryInterface;

/**
 * Creates a new Comment and persists it via the repository.
 */
final class CreateCommentHandler
{
    /**
     * @param CommentRepositoryInterface $repository Persists and retrieves Comment domain entities.
     */
    public function __construct(
        private readonly CommentRepositoryInterface $repository,
    ) {}

    /**
     * Creates a new Comment from the command payload and returns it as a DTO.
     *
     * @param  CreateCommentCommand $command The command carrying collection, item, text, and userId.
     * @return CommentDto                    DTO representation of the newly created comment.
     */
    public function handle(CreateCommentCommand $command): CommentDto
    {
        $comment = new Comment(
            $command->collection,
            $command->item,
            $command->comment,
            $command->userId,
        );

        $this->repository->save($comment);

        return CommentDto::fromEntity($comment);
    }
}
