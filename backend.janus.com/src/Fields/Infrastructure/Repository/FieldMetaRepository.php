<?php

declare(strict_types=1);

namespace App\Fields\Infrastructure\Repository;

use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FieldMeta>
 */
final class FieldMetaRepository extends ServiceEntityRepository implements FieldMetaRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FieldMeta::class);
    }

    public function save(FieldMeta $field, bool $flush = true): void
    {
        $this->getEntityManager()->persist($field);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete(FieldMeta $field): void
    {
        $this->getEntityManager()->remove($field);
        $this->getEntityManager()->flush();
    }

    public function findByCollectionAndField(string $collection, string $field): ?FieldMeta
    {
        return $this->findOneBy(['collection' => $collection, 'field' => $field]);
    }

    /** @return FieldMeta[] */
    public function findByCollection(string $collection): array
    {
        return $this->findBy(['collection' => $collection], ['sortOrder' => 'ASC', 'createdAt' => 'ASC']);
    }

    /** @return FieldMeta[] */
    public function findPaginated(int $limit, int $offset): array
    {
        return $this->findBy([], ['collection' => 'ASC', 'sortOrder' => 'ASC'], $limit, $offset);
    }

    public function countAll(): int
    {
        return $this->count([]);
    }

    public function deleteByCollection(string $collection): void
    {
        $this->createQueryBuilder('f')
            ->delete()
            ->where('f.collection = :collection')
            ->setParameter('collection', $collection)
            ->getQuery()
            ->execute();
    }
}
