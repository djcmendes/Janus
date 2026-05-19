<?php

/**
 * @file ActivityRepository.php
 *
 * Doctrine ORM implementation of ActivityRepositoryInterface.
 * Bridges the domain layer and the database via ActivityMapper and ActivityEntity.
 *
 * @package App\Activity\Infrastructure\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Repository;

use App\Activity\Domain\Entity\Activity;
use App\Activity\Domain\Repository\ActivityRepositoryInterface;
use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use App\Activity\Infrastructure\Persistence\Doctrine\Mapper\ActivityMapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine ORM repository for Activity records.
 *
 * Persists and retrieves ActivityEntity models via Doctrine, using ActivityMapper
 * to convert between the Doctrine model and the pure Activity domain entity.
 * All queries are ordered by timestamp descending so callers receive the most
 * recent entries first without needing to specify sorting.
 *
 * @extends ServiceEntityRepository<ActivityEntity>
 */
final class ActivityRepository extends ServiceEntityRepository implements ActivityRepositoryInterface
{
    /**
     * Constructor
     *
     * @param ManagerRegistry $registry Doctrine's entity manager registry.
     * @param ActivityMapper  $mapper   Converts between domain Activity and Doctrine ActivityEntity.
     */
    public function __construct(
        ManagerRegistry                 $registry,
        private readonly ActivityMapper $mapper,
    ) {
        parent::__construct(registry: $registry, entityClass: ActivityEntity::class);
    }

    /**
     * Persists a new Activity record to the database immediately.
     *
     * @param Activity $activity The domain entity to store.
     *
     * @return void
     */
    public function record(Activity $activity): void
    {
        $entity = $this->mapper->toPersistence(domain: $activity);

        $this->getEntityManager()
             ->persist(object: $entity);

        $this->getEntityManager()
             ->flush();
    }

    /**
     * Finds a single Activity record by its UUID primary key.
     *
     * @param string $id The UUID of the activity record to retrieve.
     *
     * @return Activity|null The matching domain entity, or null if no record exists.
     */
    public function findById(string $id): ?Activity
    {
        $entity = $this->find(id: $id);

        return $entity !== null ? $this->mapper->toDomain(entity: $entity) : null;
    }

    /**
     * Returns a page of Activity domain entities, ordered by timestamp descending,
     * with optional filters applied.
     *
     * @param int         $limit      Maximum number of records to return.
     * @param int         $offset     Number of records to skip (pagination offset).
     * @param string|null $collection Filter to activities on a specific collection name, or null to include all.
     * @param string|null $action     Filter to a specific action type (e.g. 'create', 'update'), or null to include all.
     * @param string|null $userId     Filter to activities performed by a specific user UUID, or null to include all.
     *
     * @return array<Activity> Ordered array of domain Activity entities matching the given criteria.
     */
    public function findPaginated(
        int     $limit,
        int     $offset,
        ?string $collection = null,
        ?string $action     = null,
        ?string $userId     = null,
    ): array {
        $qb = $this->createQueryBuilder(alias: 'a')
                   ->orderBy(sort: 'a.timestamp', order:'DESC')
                   ->setMaxResults(maxResults: $limit)
                   ->setFirstResult(firstResult: $offset);

        if ($collection !== null) {
            $qb->andWhere('a.collection = :collection')
               ->setParameter('collection', $collection);
        }

        if ($action !== null) {
            $qb->andWhere('a.action = :action')
               ->setParameter('action', $action);
        }

        if ($userId !== null) {
            $qb->andWhere('a.userId = :userId')
               ->setParameter('userId', $userId);
        }

        $results = $qb->getQuery()
                      ->getResult();

        /** @var array<int, ActivityEntity> $results */
        return array_map(callback: $this->mapper->toDomain(...), array: $results);
    }

    /**
     * Returns the total count of Activity records matching the given filters,
     * used to populate the `total_count` meta field in paginated responses.
     *
     * @param string|null $collection Filter to activities on a specific collection name, or null for all.
     * @param string|null $action     Filter to a specific action type, or null for all.
     * @param string|null $userId     Filter to a specific user UUID, or null for all.
     *
     * @return int Total number of matching records.
     */
    public function countAll(
        ?string $collection = null,
        ?string $action     = null,
        ?string $userId     = null,
    ): int {
        $qb = $this->createQueryBuilder(alias: 'a')
                   ->select(select: 'COUNT(a.id)');

        if ($collection !== null) {
            $qb->andWhere('a.collection = :collection')
               ->setParameter('collection', $collection);
        }
        if ($action !== null) {
            $qb->andWhere('a.action = :action')
               ->setParameter('action', $action);
        }
        if ($userId !== null) {
            $qb->andWhere('a.userId = :userId')
               ->setParameter('userId', $userId);
        }

        return (int) $qb->getQuery()
                        ->getSingleScalarResult();
    }
}
