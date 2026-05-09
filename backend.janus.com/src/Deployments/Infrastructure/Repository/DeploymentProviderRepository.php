<?php

/**
 * @file DeploymentProviderRepository.php
 *
 * Doctrine ORM implementation of DeploymentProviderRepositoryInterface.
 *
 * @package App\Deployments\Infrastructure\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Repository;

use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Domain\Repository\DeploymentProviderRepositoryInterface;
use App\Deployments\Infrastructure\Persistence\Doctrine\Entity\DeploymentProviderEntity;
use App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\DeploymentProviderMapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine-backed repository for DeploymentProvider persistence operations.
 *
 * All read results are converted to domain DeploymentProvider objects via DeploymentProviderMapper
 * before being returned, keeping Doctrine concerns out of the domain layer.
 *
 * @extends ServiceEntityRepository<DeploymentProviderEntity>
 */
final class DeploymentProviderRepository extends ServiceEntityRepository implements DeploymentProviderRepositoryInterface
{
    /**
     * @param ManagerRegistry          $registry Doctrine registry (injected by Symfony).
     * @param DeploymentProviderMapper $mapper   Converts between domain and persistence models.
     */
    public function __construct(ManagerRegistry $registry, private readonly DeploymentProviderMapper $mapper)
    {
        parent::__construct($registry, DeploymentProviderEntity::class);
    }

    /**
     * Persists a DeploymentProvider to the database.
     *
     * @param DeploymentProvider $provider The provider to persist.
     * @param bool               $flush    Whether to flush immediately (default true).
     *
     * @return void
     */
    public function save(DeploymentProvider $provider, bool $flush = true): void
    {
        $entity = $this->mapper->toPersistence($provider);
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Removes a DeploymentProvider from the database by its ID.
     *
     * @param DeploymentProvider $provider The provider to remove.
     *
     * @return void
     */
    public function delete(DeploymentProvider $provider): void
    {
        $entity = $this->find($provider->getId());

        if ($entity !== null) {
            $this->getEntityManager()->remove($entity);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Finds a DeploymentProvider by its UUID, or returns null when not found.
     *
     * @param  string                  $id UUID of the provider to retrieve.
     * @return DeploymentProvider|null     The domain entity, or null.
     */
    public function findById(string $id): ?DeploymentProvider
    {
        $entity = $this->find($id);
        return $entity !== null ? $this->mapper->toDomain($entity) : null;
    }

    /**
     * Returns a paginated slice of deployment providers ordered by creation date.
     *
     * @param  int                  $limit  Maximum number of records to return.
     * @param  int                  $offset Zero-based record offset.
     * @return DeploymentProvider[]         Array of domain DeploymentProvider entities.
     */
    public function findPaginated(int $limit, int $offset): array
    {
        $results = $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();

        return array_map($this->mapper->toDomain(...), $results);
    }

    /**
     * Counts the total number of deployment providers.
     *
     * @return int Total count.
     */
    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
