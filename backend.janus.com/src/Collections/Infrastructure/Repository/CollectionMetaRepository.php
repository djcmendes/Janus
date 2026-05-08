<?php

/**
 * @file CollectionMetaRepository.php
 *
 * Doctrine ORM implementation of CollectionMetaRepositoryInterface.
 * Bridges the domain layer and the database via CollectionMetaMapper and CollectionMetaEntity.
 *
 * @package App\Collections\Infrastructure\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Repository;

use App\Collections\Domain\Entity\CollectionMeta;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Collections\Infrastructure\Persistence\Doctrine\Entity\CollectionMetaEntity;
use App\Collections\Infrastructure\Persistence\Doctrine\Mapper\CollectionMetaMapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * Doctrine ORM repository for CollectionMeta records.
 *
 * Persists and retrieves CollectionMetaEntity models via Doctrine, using
 * CollectionMetaMapper to convert between the Doctrine model and the pure
 * CollectionMeta domain entity. All queries are ordered by createdAt ascending
 * so callers receive the oldest entries first without needing to specify sorting.
 *
 * @extends ServiceEntityRepository<CollectionMetaEntity>
 * @implements CollectionMetaRepositoryInterface
 */
final class CollectionMetaRepository extends ServiceEntityRepository implements CollectionMetaRepositoryInterface
{
    /**
     * Constructor
     *
     * @param ManagerRegistry       $registry Doctrine's entity manager registry.
     * @param CollectionMetaMapper  $mapper   Converts between domain CollectionMeta and Doctrine CollectionMetaEntity.
     */
    public function __construct(
        ManagerRegistry                    $registry,
        private readonly CollectionMetaMapper $mapper,
    ) {
        parent::__construct(registry: $registry, entityClass: CollectionMetaEntity::class);
    }

    /**
     * Persists a CollectionMeta record to the database, inserting or updating as needed.
     *
     * When an entity with the same UUID already exists in Doctrine's identity map, only
     * the mutable fields are updated on the tracked entity. Otherwise a new entity is
     * created via the mapper and persisted.
     *
     * @param CollectionMeta $collection The domain entity to store.
     * @param bool           $flush      Whether to flush immediately (default: true).
     *
     * @return void
     */
    public function save(CollectionMeta $collection, bool $flush = true): void
    {
        $entity = $this->find($collection->getId());

        if ($entity === null) {
            $entity = $this->mapper->toPersistence($collection);
            $this->getEntityManager()->persist($entity);
        } else {
            $entity
                ->setLabel($collection->getLabel())
                ->setIcon($collection->getIcon())
                ->setNote($collection->getNote())
                ->setHidden($collection->isHidden())
                ->setSingleton($collection->isSingleton())
                ->setSortField($collection->getSortField())
                ->setUpdatedAt($collection->getUpdatedAt());
        }

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Removes a CollectionMeta record from the database immediately.
     *
     * @param CollectionMeta $collection The domain entity whose persisted counterpart should be removed.
     *
     * @return void
     */
    public function delete(CollectionMeta $collection): void
    {
        $entity = $this->find($collection->getId());

        if ($entity !== null) {
            $this->getEntityManager()->remove($entity);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Finds a single CollectionMeta record by its collection name.
     *
     * @param string $name The collection name to look up.
     *
     * @return CollectionMeta|null The matching domain entity, or null if no record exists.
     */
    public function findByName(string $name): ?CollectionMeta
    {
        $entity = $this->findOneBy(['name' => $name]);

        return $entity !== null ? $this->mapper->toDomain($entity) : null;
    }

    /**
     * Returns a page of CollectionMeta domain entities ordered by createdAt ascending.
     *
     * @param int $limit  Maximum number of records to return.
     * @param int $offset Number of records to skip (pagination offset).
     *
     * @return CollectionMeta[] Ordered array of domain CollectionMeta entities.
     */
    public function findPaginated(int $limit, int $offset): array
    {
        $entities = $this->findBy([], ['createdAt' => 'ASC'], $limit, $offset);

        return array_map($this->mapper->toDomain(...), $entities);
    }

    /**
     * Returns the total count of CollectionMeta records matching the given criteria.
     *
     * @param array<string, mixed> $criteria Optional filter criteria passed to Doctrine.
     *
     * @return int Total number of matching records.
     */
    public function count(array $criteria = []): int
    {
        return parent::count($criteria);
    }
}
