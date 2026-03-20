<?php

declare(strict_types=1);

namespace App\Collections\Infrastructure\Repository;

use App\Collections\Domain\Entity\CollectionMeta;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CollectionMeta>
 */
final class CollectionMetaRepository extends ServiceEntityRepository implements CollectionMetaRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollectionMeta::class);
    }

    public function save(CollectionMeta $collection, bool $flush = true): void
    {
        $this->getEntityManager()->persist($collection);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete(CollectionMeta $collection): void
    {
        $this->getEntityManager()->remove($collection);
        $this->getEntityManager()->flush();
    }

    public function findByName(string $name): ?CollectionMeta
    {
        return $this->findOneBy(['name' => $name]);
    }

    /** @return CollectionMeta[] */
    public function findPaginated(int $limit, int $offset): array
    {
        return $this->findBy([], ['createdAt' => 'ASC'], $limit, $offset);
    }

    public function count(array $criteria = []): int
    {
        return parent::count($criteria);
    }
}
