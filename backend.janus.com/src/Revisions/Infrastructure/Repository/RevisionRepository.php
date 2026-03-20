<?php

declare(strict_types=1);

namespace App\Revisions\Infrastructure\Repository;

use App\Revisions\Domain\Entity\Revision;
use App\Revisions\Domain\Repository\RevisionRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Revision>
 */
final class RevisionRepository extends ServiceEntityRepository implements RevisionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Revision::class);
    }

    public function record(Revision $revision): void
    {
        $this->getEntityManager()->persist($revision);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Revision
    {
        return $this->find($id);
    }

    public function findLatestForItem(string $collection, string $item): ?Revision
    {
        return $this->createQueryBuilder('r')
            ->where('r.collection = :collection')
            ->andWhere('r.item = :item')
            ->setParameter('collection', $collection)
            ->setParameter('item', $item)
            ->orderBy('r.version', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return Revision[] */
    public function findAll(
        int     $limit,
        int     $offset,
        ?string $collection = null,
        ?string $item       = null,
    ): array {
        $qb = $this->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($collection !== null) {
            $qb->andWhere('r.collection = :collection')->setParameter('collection', $collection);
        }
        if ($item !== null) {
            $qb->andWhere('r.item = :item')->setParameter('item', $item);
        }

        return $qb->getQuery()->getResult();
    }

    public function countAll(
        ?string $collection = null,
        ?string $item       = null,
    ): int {
        $qb = $this->createQueryBuilder('r')->select('COUNT(r.id)');

        if ($collection !== null) {
            $qb->andWhere('r.collection = :collection')->setParameter('collection', $collection);
        }
        if ($item !== null) {
            $qb->andWhere('r.item = :item')->setParameter('item', $item);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
