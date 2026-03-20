<?php

declare(strict_types=1);

namespace App\Users\Domain\Repository;

use App\Users\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function save(User $user, bool $flush = true): void;

    public function findActiveById(string $id): ?User;

    public function findByEmail(string $email): ?User;

    /** @return User[] */
    public function findAllActive(int $limit, int $offset): array;

    public function countActive(): int;

    public function findByInviteToken(string $token): ?User;
}
