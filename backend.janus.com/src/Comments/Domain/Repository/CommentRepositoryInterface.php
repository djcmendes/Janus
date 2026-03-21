<?php

declare(strict_types=1);

namespace App\Comments\Domain\Repository;

use App\Comments\Domain\Entity\Comment;

interface CommentRepositoryInterface
{
    public function save(Comment $comment, bool $flush = true): void;
    public function delete(Comment $comment): void;
    public function findById(string $id): ?Comment;

    /** @return Comment[] */
    public function findPaginated(
        int     $limit,
        int     $offset,
        ?string $collection = null,
        ?string $item       = null,
    ): array;

    public function countAll(
        ?string $collection = null,
        ?string $item       = null,
    ): int;
}
