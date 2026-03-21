<?php

declare(strict_types=1);

namespace App\Flows\Infrastructure\Repository;

use App\Flows\Domain\Entity\Operation;
use App\Flows\Domain\Repository\OperationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Operation>
 */
final class OperationRepository extends ServiceEntityRepository implements OperationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Operation::class);
    }

    public function save(Operation $operation): void
    {
        $this->getEntityManager()->persist($operation);
        $this->getEntityManager()->flush();
    }

    public function delete(Operation $operation): void
    {
        $this->getEntityManager()->remove($operation);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Operation
    {
        return $this->find($id);
    }

    /** @return Operation[] */
    public function findPaginated(int $limit, int $offset, ?string $flowId = null): array
    {
        $qb = $this->createQueryBuilder('o')
            ->orderBy('o.sortOrder', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($flowId !== null) {
            $qb->andWhere('o.flowId = :flowId')->setParameter('flowId', $flowId);
        }

        return $qb->getQuery()->getResult();
    }

    public function countAll(?string $flowId = null): int
    {
        $qb = $this->createQueryBuilder('o')->select('COUNT(o.id)');

        if ($flowId !== null) {
            $qb->andWhere('o.flowId = :flowId')->setParameter('flowId', $flowId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function deleteByFlow(string $flowId): void
    {
        $this->createQueryBuilder('o')
            ->delete()
            ->where('o.flowId = :flowId')
            ->setParameter('flowId', $flowId)
            ->getQuery()
            ->execute();
    }
}
