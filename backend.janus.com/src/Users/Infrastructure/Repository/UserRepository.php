<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Repository;

use App\Users\Domain\Entity\User;
use App\Users\Domain\Repository\UserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
final class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $user, bool $flush = true): void
    {
        $this->getEntityManager()->persist($user);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email, 'deletedAt' => null]);
    }

    public function findActiveById(string $id): ?User
    {
        return $this->findOneBy(['id' => $id, 'deletedAt' => null]);
    }

    /** @return User[] */
    public function findAllActive(int $limit, int $offset): array
    {
        return $this->findBy(['deletedAt' => null], ['createdAt' => 'DESC'], $limit, $offset);
    }

    public function countActive(): int
    {
        return $this->count(['deletedAt' => null]);
    }

    public function findByInviteToken(string $token): ?User
    {
        return $this->findOneBy(['inviteToken' => $token, 'deletedAt' => null]);
    }
}
