<?php

/**
 * @file VersionRepository.php
 *
 * Doctrine ORM implementation of VersionRepositoryInterface.
 * Bridges the domain layer and the database via VersionMapper and VersionEntity.
 *
 * @package App\Versions\Infrastructure\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Repository;

use App\Versions\Domain\Entity\Version;
use App\Versions\Domain\Repository\VersionRepositoryInterface;
use App\Versions\Infrastructure\Persistence\Doctrine\Entity\VersionEntity;
use App\Versions\Infrastructure\Persistence\Doctrine\Mapper\VersionMapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine ORM repository for Version records.
 *
 * Persists and retrieves VersionEntity models via Doctrine, using VersionMapper
 * to convert between the Doctrine model and the pure Version domain entity.
 * All queries are ordered by createdAt descending so callers receive the most
 * recent entries first without needing to specify sorting.
 *
 * @extends ServiceEntityRepository<VersionEntity>
 * @implements VersionRepositoryInterface
 */
final class VersionRepository extends ServiceEntityRepository implements VersionRepositoryInterface
{
    /**
     * @param ManagerRegistry $registry Doctrine's entity manager registry.
     * @param VersionMapper   $mapper   Converts between domain Version and Doctrine VersionEntity.
     */
    public function __construct(
        ManagerRegistry              $registry,
        private readonly VersionMapper $mapper,
    ) {
        parent::__construct(registry: $registry, entityClass: VersionEntity::class);
    }

    /**
     * Persists a Version record to the database, inserting or updating as needed.
     *
     * When an entity with the same UUID already exists in Doctrine's identity map,
     * only the mutable fields (key, delta, updatedAt) are updated on the tracked entity.
     * Otherwise a new entity is created via the mapper and persisted.
     *
     * @param Version $version The domain entity to store.
     * @param bool    $flush   Whether to flush immediately (default: true).
     *
     * @return void
     */
    public function save(Version $version, bool $flush = true): void
    {
        $entity = $this->find($version->getId());

        if ($entity === null) {
            $entity = $this->mapper->toPersistence($version);
            $this->getEntityManager()->persist($entity);
        } else {
            $entity
                ->setKey($version->getKey())
                ->setDelta($version->getDelta())
                ->setUpdatedAt($version->getUpdatedAt());
        }

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Removes a Version record from the database immediately.
     *
     * @param Version $version The domain entity whose persisted counterpart should be removed.
     *
     * @return void
     */
    public function delete(Version $version): void
    {
        $entity = $this->find($version->getId());

        if ($entity !== null) {
            $this->getEntityManager()->remove($entity);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Finds a single Version record by its UUID primary key.
     *
     * @param string $id The UUID of the version record to retrieve.
     *
     * @return Version|null The matching domain entity, or null if no record exists.
     */
    public function findById(string $id): ?Version
    {
        $entity = $this->find($id);

        return $entity !== null ? $this->mapper->toDomain($entity) : null;
    }

    /**
     * Finds a Version by the unique collection + item + key triplet.
     *
     * @param string $collection Collection name to search within.
     * @param string $item       Item identifier to match.
     * @param string $key        Version label to match.
     *
     * @return Version|null The matching domain entity, or null if none exists.
     */
    public function findByCollectionItemAndKey(string $collection, string $item, string $key): ?Version
    {
        $entity = $this->findOneBy([
            'collection' => $collection,
            'item'       => $item,
            'key'        => $key,
        ]);

        return $entity !== null ? $this->mapper->toDomain($entity) : null;
    }

    /**
     * Returns a page of Version domain entities ordered by createdAt descending.
     *
     * @param int         $limit      Maximum number of records to return.
     * @param int         $offset     Number of records to skip (pagination offset).
     * @param string|null $collection Filter to versions targeting a specific collection, or null for all.
     * @param string|null $item       Filter to versions for a specific item identifier, or null for all.
     *
     * @return Version[] Ordered array of domain Version entities matching the given criteria.
     */
    public function findPaginated(int $limit, int $offset, ?string $collection = null, ?string $item = null): array
    {
        $qb = $this->createQueryBuilder('v')
                   ->orderBy('v.createdAt', 'DESC');

        if ($collection !== null) {
            $qb->andWhere('v.collection = :collection')
               ->setParameter('collection', $collection);
        }

        if ($item !== null) {
            $qb->andWhere('v.item = :item')
               ->setParameter('item', $item);
        }

        return array_map(
            $this->mapper->toDomain(...),
            $qb->setMaxResults($limit)
               ->setFirstResult($offset)
               ->getQuery()
               ->getResult(),
        );
    }

    /**
     * Returns the total count of Version records matching the given filters,
     * used to populate the `total_count` meta field in paginated responses.
     *
     * @param string|null $collection Filter to a specific collection name, or null for all.
     * @param string|null $item       Filter to a specific item identifier, or null for all.
     *
     * @return int Total number of matching records.
     */
    public function countAll(?string $collection = null, ?string $item = null): int
    {
        $qb = $this->createQueryBuilder('v')
                   ->select('COUNT(v.id)');

        if ($collection !== null) {
            $qb->andWhere('v.collection = :collection')
               ->setParameter('collection', $collection);
        }

        if ($item !== null) {
            $qb->andWhere('v.item = :item')
               ->setParameter('item', $item);
        }

        return (int) $qb->getQuery()
                        ->getSingleScalarResult();
    }
}
