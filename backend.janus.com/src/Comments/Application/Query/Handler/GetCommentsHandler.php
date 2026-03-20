<?php

declare(strict_types=1);

namespace App\Comments\Application\Query\Handler;

use App\Comments\Application\DTO\CommentDto;
use App\Comments\Application\Query\GetCommentsQuery;
use App\Comments\Domain\Repository\CommentRepositoryInterface;

final class GetCommentsHandler
{
    public function __construct(
        private readonly CommentRepositoryInterface $repository,
    ) {}

    /** @return array{data: CommentDto[], total: int} */
    public function handle(GetCommentsQuery $query): array
    {
        $comments = $this->repository->findAll(
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
