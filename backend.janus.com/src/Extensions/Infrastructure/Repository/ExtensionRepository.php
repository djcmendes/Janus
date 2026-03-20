<?php

declare(strict_types=1);

namespace App\Extensions\Infrastructure\Repository;

use App\Extensions\Domain\Entity\Extension;
use App\Extensions\Domain\Repository\ExtensionRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Extension>
 */
final class ExtensionRepository extends ServiceEntityRepository implements ExtensionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Extension::class);
    }

    public function save(Extension $extension): void
    {
        $this->getEntityManager()->persist($extension);
        $this->getEntityManager()->flush();
    }

    public function delete(Extension $extension): void
    {
        $this->getEntityManager()->remove($extension);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Extension
    {
        return $this->find($id);
    }

    /** @return Extension[] */
    public function findPaginated(
        int     $limit,
        int     $offset,
        ?string $type    = null,
        ?bool   $enabled = null,
    ): array {
        $qb = $this->createQueryBuilder('e')
            ->orderBy('e.name', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($type !== null) {
            $qb->andWhere('e.type = :type')->setParameter('type', $type);
        }
        if ($enabled !== null) {
            $qb->andWhere('e.enabled = :enabled')->setParameter('enabled', $enabled);
        }

        return $qb->getQuery()->getResult();
    }

    public function countAll(
        ?string $type    = null,
        ?bool   $enabled = null,
    ): int {
        $qb = $this->createQueryBuilder('e')->select('COUNT(e.id)');

        if ($type !== null) {
            $qb->andWhere('e.type = :type')->setParameter('type', $type);
        }
        if ($enabled !== null) {
            $qb->andWhere('e.enabled = :enabled')->setParameter('enabled', $enabled);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
