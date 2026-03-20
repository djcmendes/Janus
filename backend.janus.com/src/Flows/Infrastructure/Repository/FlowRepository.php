<?php

declare(strict_types=1);

namespace App\Flows\Infrastructure\Repository;

use App\Flows\Domain\Entity\Flow;
use App\Flows\Domain\Repository\FlowRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Flow>
 */
final class FlowRepository extends ServiceEntityRepository implements FlowRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flow::class);
    }

    public function save(Flow $flow): void
    {
        $this->getEntityManager()->persist($flow);
        $this->getEntityManager()->flush();
    }

    public function delete(Flow $flow): void
    {
        $this->getEntityManager()->remove($flow);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Flow
    {
        return $this->find($id);
    }

    /** @return Flow[] */
    public function findPaginated(int $limit, int $offset, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('f')
            ->orderBy('f.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($status !== null) {
            $qb->andWhere('f.status = :status')->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }

    public function countAll(?string $status = null): int
    {
        $qb = $this->createQueryBuilder('f')->select('COUNT(f.id)');

        if ($status !== null) {
            $qb->andWhere('f.status = :status')->setParameter('status', $status);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
