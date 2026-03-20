<?php

declare(strict_types=1);

namespace App\Policies\Infrastructure\Repository;

use App\Policies\Domain\Entity\Policy;
use App\Policies\Domain\Repository\PolicyRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Policy>
 */
final class PolicyRepository extends ServiceEntityRepository implements PolicyRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Policy::class);
    }

    public function save(Policy $policy, bool $flush = true): void
    {
        $this->getEntityManager()->persist($policy);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete(Policy $policy): void
    {
        $this->getEntityManager()->remove($policy);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Policy
    {
        return $this->find($id);
    }

    public function findByName(string $name): ?Policy
    {
        return $this->findOneBy(['name' => $name]);
    }

    /** @return Policy[] */
    public function findAll(int $limit, int $offset): array
    {
        return $this->findBy([], ['createdAt' => 'ASC'], $limit, $offset);
    }

    public function count(array $criteria = []): int
    {
        return parent::count($criteria);
    }
}
