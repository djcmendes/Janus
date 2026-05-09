<?php

/**
 * @file GetCommentsHandler.php
 *
 * Application handler for the list-comments query.
 *
 * @package App\Comments\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Query\Handler;

use App\Comments\Application\DTO\CommentDto;
use App\Comments\Application\Query\GetCommentsQuery;
use App\Comments\Domain\Repository\CommentRepositoryInterface;

/**
 * Retrieves a paginated list of comments with optional collection and item filters.
 */
final class GetCommentsHandler
{
    /**
     * @param CommentRepositoryInterface $repository Persists and retrieves Comment domain entities.
     */
    public function __construct(
        private readonly CommentRepositoryInterface $repository,
    ) {}

    /**
     * Returns a paginated array of CommentDto objects and the total matching count.
     *
     * @param  GetCommentsQuery $query The query carrying limit, offset, and optional filters.
     * @return array{data: CommentDto[], total: int} Paginated data and total count for meta.
     */
    public function handle(GetCommentsQuery $query): array
    {
        $comments = $this->repository->findPaginated(
            $query->limit,
            $query->offset,
            $query->collection,
            $query->item,
        );

        $total = $this->repository->countAll(
            $query->collection,
            $query->item,
        );

        return [
            'data'  => array_map(CommentDto::fromEntity(...), $comments),
            'total' => $total,
        ];
    }
}
