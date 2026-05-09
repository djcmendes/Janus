<?php

/**
 * @file DeploymentRepository.php
 *
 * Doctrine ORM implementation of DeploymentRepositoryInterface.
 *
 * @package App\Deployments\Infrastructure\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Repository;

use App\Deployments\Domain\Entity\Deployment;
use App\Deployments\Domain\Repository\DeploymentRepositoryInterface;
use App\Deployments\Infrastructure\Persistence\Doctrine\Entity\DeploymentEntity;
use App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\DeploymentMapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine-backed repository for Deployment run persistence operations.
 *
 * All read results are converted to domain Deployment objects via DeploymentMapper
 * before being returned, keeping Doctrine concerns out of the domain layer.
 *
 * @extends ServiceEntityRepository<DeploymentEntity>
 */
final class DeploymentRepository extends ServiceEntityRepository implements DeploymentRepositoryInterface
{
    /**
     * @param ManagerRegistry  $registry Doctrine registry (injected by Symfony).
     * @param DeploymentMapper $mapper   Converts between domain and persistence models.
     */
    public function __construct(ManagerRegistry $registry, private readonly DeploymentMapper $mapper)
    {
        parent::__construct($registry, DeploymentEntity::class);
    }

    /**
     * Persists a Deployment run record to the database.
     *
     * @param Deployment $deployment The deployment run to persist.
     * @param bool       $flush      Whether to flush immediately (default true).
     *
     * @return void
     */
    public function save(Deployment $deployment, bool $flush = true): void
    {
        $entity = $this->mapper->toPersistence($deployment);
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Finds a Deployment run by its UUID, or returns null when not found.
     *
     * @param  string          $id UUID of the deployment run to retrieve.
     * @return Deployment|null     The domain entity, or null.
     */
    public function findById(string $id): ?Deployment
    {
        $entity = $this->find($id);
        return $entity !== null ? $this->mapper->toDomain($entity) : null;
    }

    /**
     * Returns a paginated slice of deployment runs, optionally filtered by provider.
     *
     * @param  int         $limit      Maximum number of records to return.
     * @param  int         $offset     Zero-based record offset.
     * @param  string|null $providerId Provider UUID filter; null returns all runs.
     * @return Deployment[]             Array of domain Deployment entities.
     */
    public function findPaginated(int $limit, int $offset, ?string $providerId = null): array
    {
        $qb = $this->createQueryBuilder('d')->orderBy('d.startedAt', 'DESC');

        if ($providerId !== null) {
            $qb->andWhere('d.providerId = :pid')->setParameter('pid', $providerId);
        }

        return array_map(
            $this->mapper->toDomain(...),
            $qb->setMaxResults($limit)->setFirstResult($offset)->getQuery()->getResult(),
        );
    }

    /**
     * Counts total deployment runs, optionally filtered by provider.
     *
     * @param  string|null $providerId Provider UUID filter; null counts all runs.
     * @return int                      Total number of matching runs.
     */
    public function countAll(?string $providerId = null): int
    {
        $qb = $this->createQueryBuilder('d')->select('COUNT(d.id)');

        if ($providerId !== null) {
            $qb->andWhere('d.providerId = :pid')->setParameter('pid', $providerId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
