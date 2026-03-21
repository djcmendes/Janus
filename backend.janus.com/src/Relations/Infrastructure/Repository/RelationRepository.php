<?php

declare(strict_types=1);

namespace App\Relations\Infrastructure\Repository;

use App\Relations\Domain\Entity\Relation;
use App\Relations\Domain\Repository\RelationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Relation>
 */
final class RelationRepository extends ServiceEntityRepository implements RelationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Relation::class);
    }

    public function save(Relation $relation, bool $flush = true): void
    {
        $this->getEntityManager()->persist($relation);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete(Relation $relation): void
    {
        $this->getEntityManager()->remove($relation);
        $this->getEntityManager()->flush();
    }

    public function findByCollectionAndField(string $manyCollection, string $manyField): ?Relation
    {
        return $this->findOneBy(['manyCollection' => $manyCollection, 'manyField' => $manyField]);
    }

    /** @return Relation[] */
    public function findPaginated(int $limit, int $offset): array
    {
        return $this->findBy([], ['manyCollection' => 'ASC', 'manyField' => 'ASC'], $limit, $offset);
    }

    public function count(array $criteria = []): int
    {
        return parent::count($criteria);
    }

    public function deleteByCollection(string $collection): void
    {
        $this->createQueryBuilder('r')
            ->delete()
            ->where('r.manyCollection = :col OR r.oneCollection = :col OR r.junctionCollection = :col')
            ->setParameter('col', $collection)
            ->getQuery()
            ->execute();
    }
}
