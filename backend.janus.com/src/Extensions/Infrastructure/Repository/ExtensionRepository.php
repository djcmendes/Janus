<?php

/**
 * @file ExtensionRepository.php
 *
 * Doctrine ORM implementation of ExtensionRepositoryInterface.
 *
 * @package App\Extensions\Infrastructure\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Infrastructure\Repository;

use App\Extensions\Domain\Entity\Extension;
use App\Extensions\Domain\Repository\ExtensionRepositoryInterface;
use App\Extensions\Infrastructure\Persistence\Doctrine\Entity\ExtensionEntity;
use App\Extensions\Infrastructure\Persistence\Doctrine\Mapper\ExtensionMapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine-backed implementation of ExtensionRepositoryInterface.
 *
 * All reads return mapped domain entities via ExtensionMapper; all writes convert
 * domain entities to ExtensionEntity before delegating to the EntityManager.
 *
 * @extends ServiceEntityRepository<ExtensionEntity>
 */
final class ExtensionRepository extends ServiceEntityRepository implements ExtensionRepositoryInterface
{
    /**
     * @param ManagerRegistry $registry Doctrine registry for entity manager resolution.
     * @param ExtensionMapper $mapper   Translates between domain and Doctrine entities.
     */
    public function __construct(
        ManagerRegistry              $registry,
        private readonly ExtensionMapper $mapper,
    ) {
        parent::__construct($registry, ExtensionEntity::class);
    }

    /**
     * Persists an Extension domain entity.
     *
     * @param Extension $extension The extension to persist.
     */
    public function save(Extension $extension): void
    {
        $entity = $this->mapper->toPersistence($extension);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Removes an Extension domain entity from persistence.
     *
     * @param Extension $extension The extension to delete.
     */
    public function delete(Extension $extension): void
    {
        $entity = $this->mapper->toPersistence($extension);
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Finds an Extension by UUID, or returns null when not found.
     *
     * @param  string         $id UUID of the extension to retrieve.
     * @return Extension|null     The mapped domain entity, or null.
     */
    public function findById(string $id): ?Extension
    {
        $entity = $this->find($id);

        return $entity !== null ? $this->mapper->toDomain($entity) : null;
    }

    /**
     * Returns a paginated slice of extensions, optionally filtered by type and enabled state.
     *
     * @param  int         $limit   Maximum number of records.
     * @param  int         $offset  Zero-based record offset.
     * @param  string|null $type    Filter by ExtensionType value; null returns all types.
     * @param  bool|null   $enabled Filter by enabled state; null returns all.
     * @return Extension[]           Array of matching domain entities.
     */
    public function findPaginated(
        int     $limit,
        int     $offset,
        ?string $type    = null,
        ?bool   $enabled = null,
    ): array {
        $qb = $this->createQueryBuilder('e')
            ->orderBy('e.name', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($type !== null) {
            $qb->andWhere('e.type = :type')->setParameter('type', $type);
        }
        if ($enabled !== null) {
            $qb->andWhere('e.enabled = :enabled')->setParameter('enabled', $enabled);
        }

        /** @var ExtensionEntity[] $entities */
        $entities = $qb->getQuery()->getResult();

        return array_map($this->mapper->toDomain(...), $entities);
    }

    /**
     * Counts total extensions, optionally filtered by type and enabled state.
     *
     * @param  string|null $type    Filter by ExtensionType value; null counts all types.
     * @param  bool|null   $enabled Filter by enabled state; null counts all.
     * @return int                   Total number of matching records.
     */
    public function countAll(
        ?string $type    = null,
        ?bool   $enabled = null,
    ): int {
        $qb = $this->createQueryBuilder('e')->select('COUNT(e.id)');

        if ($type !== null) {
            $qb->andWhere('e.type = :type')->setParameter('type', $type);
        }
        if ($enabled !== null) {
            $qb->andWhere('e.enabled = :enabled')->setParameter('enabled', $enabled);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
