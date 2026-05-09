<?php

/**
 * @file DashboardRepository.php
 *
 * Doctrine ORM implementation of DashboardRepositoryInterface.
 *
 * @package App\Dashboards\Infrastructure\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Infrastructure\Repository;

use App\Dashboards\Domain\Entity\Dashboard;
use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;
use App\Dashboards\Infrastructure\Persistence\Doctrine\Entity\DashboardEntity;
use App\Dashboards\Infrastructure\Persistence\Doctrine\Mapper\DashboardMapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine-backed repository for Dashboard persistence operations.
 *
 * All read results are converted to domain Dashboard objects via DashboardMapper
 * before being returned, keeping Doctrine concerns out of the domain layer.
 *
 * @extends ServiceEntityRepository<DashboardEntity>
 */
final class DashboardRepository extends ServiceEntityRepository implements DashboardRepositoryInterface
{
    /**
     * @param ManagerRegistry $registry Doctrine registry (injected by Symfony).
     * @param DashboardMapper $mapper   Converts between domain and persistence models.
     */
    public function __construct(ManagerRegistry $registry, private readonly DashboardMapper $mapper)
    {
        parent::__construct($registry, DashboardEntity::class);
    }

    /**
     * Persists a Dashboard domain entity to the database.
     *
     * @param Dashboard $dashboard The dashboard to persist.
     *
     * @return void
     */
    public function save(Dashboard $dashboard): void
    {
        $entity = $this->mapper->toPersistence($dashboard);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Removes a Dashboard from the database by its ID.
     *
     * @param Dashboard $dashboard The dashboard to remove.
     *
     * @return void
     */
    public function delete(Dashboard $dashboard): void
    {
        $entity = $this->find($dashboard->getId());

        if ($entity !== null) {
            $this->getEntityManager()->remove($entity);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Finds a Dashboard by its UUID, or returns null when not found.
     *
     * @param  string        $id UUID of the dashboard to retrieve.
     * @return Dashboard|null    The domain entity, or null.
     */
    public function findById(string $id): ?Dashboard
    {
        $entity = $this->find($id);

        return $entity !== null ? $this->mapper->toDomain($entity) : null;
    }

    /**
     * Returns a paginated slice of dashboards, optionally filtered by owner.
     *
     * @param  int         $limit   Maximum number of records to return.
     * @param  int         $offset  Zero-based record offset.
     * @param  string|null $userId  Owner UUID filter; null returns all dashboards.
     * @return Dashboard[]          Array of domain Dashboard entities.
     */
    public function findPaginated(int $limit, int $offset, ?string $userId = null): array
    {
        $qb = $this->createQueryBuilder('d')
            ->orderBy('d.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($userId !== null) {
            $qb->andWhere('d.userId = :userId')->setParameter('userId', $userId);
        }

        return array_map($this->mapper->toDomain(...), $qb->getQuery()->getResult());
    }

    /**
     * Counts total dashboard records, optionally filtered by owner.
     *
     * @param  string|null $userId Owner UUID filter; null counts all dashboards.
     * @return int                  Total number of matching dashboards.
     */
    public function countAll(?string $userId = null): int
    {
        $qb = $this->createQueryBuilder('d')->select('COUNT(d.id)');

        if ($userId !== null) {
            $qb->andWhere('d.userId = :userId')->setParameter('userId', $userId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
