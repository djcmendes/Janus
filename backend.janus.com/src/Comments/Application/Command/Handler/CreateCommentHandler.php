<?php

declare(strict_types=1);

namespace App\Comments\Application\Command\Handler;

use App\Comments\Application\Command\CreateCommentCommand;
use App\Comments\Application\DTO\CommentDto;
use App\Comments\Domain\Entity\Comment;
use App\Comments\Domain\Repository\CommentRepositoryInterface;

final class CreateCommentHandler
{
    public function __construct(
        private readonly CommentRepositoryInterface $repository,
    ) {}

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
