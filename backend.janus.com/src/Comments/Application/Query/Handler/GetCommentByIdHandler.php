<?php

/**
 * @file GetCommentByIdHandler.php
 *
 * Application handler for the get-comment-by-id query.
 *
 * @package App\Comments\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Query\Handler;

use App\Comments\Application\DTO\CommentDto;
use App\Comments\Application\Query\GetCommentByIdQuery;
use App\Comments\Domain\Exception\CommentNotFoundException;
use App\Comments\Domain\Repository\CommentRepositoryInterface;

/**
 * Retrieves a single comment by its UUID primary key.
 */
final class GetCommentByIdHandler
{
    /**
     * @param CommentRepositoryInterface $repository Persists and retrieves Comment domain entities.
     */
    public function __construct(
        private readonly CommentRepositoryInterface $repository,
    ) {}

    /**
     * Returns the comment matching the given id as a DTO.
     *
     * @param  GetCommentByIdQuery   $query The query carrying the comment UUID.
     * @return CommentDto                   DTO representation of the found comment.
     *
     * @throws CommentNotFoundException When no comment exists for the given UUID.
     */
    public function handle(GetCommentByIdQuery $query): CommentDto
    {
        $comment = $this->repository->findById($query->id);

        if ($comment === null) {
            throw new CommentNotFoundException($query->id);
        }

        return CommentDto::fromEntity($comment);
    }
}
