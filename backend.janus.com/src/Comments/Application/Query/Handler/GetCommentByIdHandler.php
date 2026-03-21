<?php

declare(strict_types=1);

namespace App\Comments\Application\Query\Handler;

use App\Comments\Application\DTO\CommentDto;
use App\Comments\Application\Query\GetCommentByIdQuery;
use App\Comments\Domain\Exception\CommentNotFoundException;
use App\Comments\Domain\Repository\CommentRepositoryInterface;

final class GetCommentByIdHandler
{
    public function __construct(
        private readonly CommentRepositoryInterface $repository,
    ) {}

    /** @throws CommentNotFoundException */
    public function handle(GetCommentByIdQuery $query): CommentDto
    {
        $comment = $this->repository->findById($query->id);

        if ($comment === null) {
            throw new CommentNotFoundException($query->id);
        }

        return CommentDto::fromEntity($comment);
    }
}
