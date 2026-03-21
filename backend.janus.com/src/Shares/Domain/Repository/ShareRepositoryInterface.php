<?php

declare(strict_types=1);

namespace App\Shares\Domain\Repository;

use App\Shares\Domain\Entity\Share;

interface ShareRepositoryInterface
{
    public function save(Share $share): void;

    public function delete(Share $share): void;

    public function findById(string $id): ?Share;

    public function findByToken(string $token): ?Share;

    /** @return Share[] */
    public function findPaginated(
        int     $limit,
        int     $offset,
        ?string $collection = null,
        ?string $userId     = null,
    ): array;

    public function countAll(
        ?string $collection = null,
        ?string $userId     = null,
    ): int;
}
