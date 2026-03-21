<?php

declare(strict_types=1);

namespace App\Activity\Infrastructure\Repository;

use App\Activity\Domain\Entity\Activity;
use App\Activity\Domain\Repository\ActivityRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activity>
 */
final class ActivityRepository extends ServiceEntityRepository implements ActivityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    public function record(Activity $activity): void
    {
        $this->getEntityManager()->persist($activity);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Activity
    {
        return $this->find($id);
    }

    /** @return Activity[] */
    public function findPaginated(
        int     $limit,
        int     $offset,
        ?string $collection = null,
        ?string $action     = null,
        ?string $userId     = null,
    ): array {
        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.timestamp', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($collection !== null) {
            $qb->andWhere('a.collection = :collection')->setParameter('collection', $collection);
        }
        if ($action !== null) {
            $qb->andWhere('a.action = :action')->setParameter('action', $action);
        }
        if ($userId !== null) {
            $qb->andWhere('a.userId = :userId')->setParameter('userId', $userId);
        }

        return $qb->getQuery()->getResult();
    }

    public function countAll(
        ?string $collection = null,
        ?string $action     = null,
        ?string $userId     = null,
    ): int {
        $qb = $this->createQueryBuilder('a')->select('COUNT(a.id)');

        if ($collection !== null) {
            $qb->andWhere('a.collection = :collection')->setParameter('collection', $collection);
        }
        if ($action !== null) {
            $qb->andWhere('a.action = :action')->setParameter('action', $action);
        }
        if ($userId !== null) {
            $qb->andWhere('a.userId = :userId')->setParameter('userId', $userId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
