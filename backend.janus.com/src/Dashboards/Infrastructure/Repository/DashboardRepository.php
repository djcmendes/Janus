<?php

declare(strict_types=1);

namespace App\Dashboards\Infrastructure\Repository;

use App\Dashboards\Domain\Entity\Dashboard;
use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dashboard>
 */
final class DashboardRepository extends ServiceEntityRepository implements DashboardRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dashboard::class);
    }

    public function save(Dashboard $dashboard): void
    {
        $this->getEntityManager()->persist($dashboard);
        $this->getEntityManager()->flush();
    }

    public function delete(Dashboard $dashboard): void
    {
        $this->getEntityManager()->remove($dashboard);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Dashboard
    {
        return $this->find($id);
    }

    /** @return Dashboard[] */
    public function findAll(int $limit, int $offset, ?string $userId = null): array
    {
        $qb = $this->createQueryBuilder('d')
            ->orderBy('d.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($userId !== null) {
            $qb->andWhere('d.userId = :userId')->setParameter('userId', $userId);
        }

        return $qb->getQuery()->getResult();
    }

    public function countAll(?string $userId = null): int
    {
        $qb = $this->createQueryBuilder('d')->select('COUNT(d.id)');

        if ($userId !== null) {
            $qb->andWhere('d.userId = :userId')->setParameter('userId', $userId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
