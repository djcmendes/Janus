<?php

declare(strict_types=1);

namespace App\Versions\Infrastructure\Repository;

use App\Versions\Domain\Entity\Version;
use App\Versions\Domain\Repository\VersionRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class VersionRepository extends ServiceEntityRepository implements VersionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Version::class);
    }

    public function save(Version $version, bool $flush = true): void
    {
        $this->getEntityManager()->persist($version);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete(Version $version): void
    {
        $this->getEntityManager()->remove($version);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Version
    {
        return $this->find($id);
    }

    public function findByCollectionItemAndKey(string $collection, string $item, string $key): ?Version
    {
        return $this->findOneBy(['collection' => $collection, 'item' => $item, 'key' => $key]);
    }

    public function findPaginated(int $limit, int $offset, ?string $collection = null, ?string $item = null): array
    {
        $qb = $this->createQueryBuilder('v')->orderBy('v.createdAt', 'DESC');

        if ($collection !== null) {
            $qb->andWhere('v.collection = :collection')->setParameter('collection', $collection);
        }
        if ($item !== null) {
            $qb->andWhere('v.item = :item')->setParameter('item', $item);
        }

        return $qb->setMaxResults($limit)->setFirstResult($offset)->getQuery()->getResult();
    }

    public function countAll(?string $collection = null, ?string $item = null): int
    {
        $qb = $this->createQueryBuilder('v')->select('COUNT(v.id)');

        if ($collection !== null) {
            $qb->andWhere('v.collection = :collection')->setParameter('collection', $collection);
        }
        if ($item !== null) {
            $qb->andWhere('v.item = :item')->setParameter('item', $item);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
