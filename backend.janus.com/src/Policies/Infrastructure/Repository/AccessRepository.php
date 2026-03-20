<?php

declare(strict_types=1);

namespace App\Policies\Infrastructure\Repository;

use App\Policies\Domain\Entity\Access;
use App\Policies\Domain\Repository\AccessRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Access>
 */
final class AccessRepository extends ServiceEntityRepository implements AccessRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Access::class);
    }

    public function save(Access $access, bool $flush = true): void
    {
        $this->getEntityManager()->persist($access);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete(Access $access): void
    {
        $this->getEntityManager()->remove($access);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Access
    {
        return $this->find($id);
    }

    public function findByRoleAndPolicy(string $roleId, string $policyId): ?Access
    {
        $qb = $this->createQueryBuilder('a')
            ->join('a.policy', 'p')
            ->where('p.id = :policyId')
            ->setParameter('policyId', $policyId);

        if ($roleId === 'public') {
            $qb->andWhere('a.role IS NULL');
        } else {
            $qb->join('a.role', 'r')
               ->andWhere('r.id = :roleId')
               ->setParameter('roleId', $roleId);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    /** @return Access[] */
    public function findAll(int $limit, int $offset): array
    {
        return $this->findBy([], ['createdAt' => 'ASC'], $limit, $offset);
    }

    public function count(array $criteria = []): int
    {
        return parent::count($criteria);
    }
}
