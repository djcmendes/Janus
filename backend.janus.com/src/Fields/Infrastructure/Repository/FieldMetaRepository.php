<?php

/**
 * @file FieldMetaRepository.php
 *
 * Doctrine ORM implementation of FieldMetaRepositoryInterface.
 * Translates between FieldMetaEntity (persistence) and FieldMeta (domain) via FieldMetaMapper.
 *
 * @package App\Fields\Infrastructure\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Infrastructure\Repository;

use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;
use App\Fields\Infrastructure\Persistence\Doctrine\Entity\FieldMetaEntity;
use App\Fields\Infrastructure\Persistence\Doctrine\Mapper\FieldMetaMapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine-backed persistence store for FieldMeta domain entities.
 *
 * All reads are converted from FieldMetaEntity to FieldMeta via FieldMetaMapper.
 * All writes convert FieldMeta to FieldMetaEntity before persisting.
 *
 * @extends ServiceEntityRepository<FieldMetaEntity>
 */
final class FieldMetaRepository extends ServiceEntityRepository implements FieldMetaRepositoryInterface
{
    /**
     * @param ManagerRegistry  $registry Doctrine manager registry.
     * @param FieldMetaMapper  $mapper   Domain ↔ persistence translator.
     */
    public function __construct(
        ManagerRegistry              $registry,
        private readonly FieldMetaMapper $mapper,
    ) {
        parent::__construct($registry, FieldMetaEntity::class);
    }

    /**
     * Persists a FieldMeta record.
     *
     * @param FieldMeta $field The field to persist.
     * @param bool      $flush Whether to flush immediately (default: true).
     */
    public function save(FieldMeta $field, bool $flush = true): void
    {
        $entity = $this->mapper->toPersistence($field);
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Removes a FieldMeta record from the store.
     *
     * @param FieldMeta $field The field to remove.
     */
    public function delete(FieldMeta $field): void
    {
        $entity = $this->mapper->toPersistence($field);
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Finds a single field by its collection and column name.
     *
     * @param  string        $collection Collection name.
     * @param  string        $field      Column name.
     * @return FieldMeta|null The matching field, or null when not found.
     */
    public function findByCollectionAndField(string $collection, string $field): ?FieldMeta
    {
        $entity = $this->findOneBy(['collection' => $collection, 'field' => $field]);
        return $entity !== null ? $this->mapper->toDomain($entity) : null;
    }

    /**
     * Returns all fields belonging to a collection, ordered by sortOrder then createdAt.
     *
     * @param  string      $collection Collection name.
     * @return FieldMeta[]             All fields for the collection.
     */
    public function findByCollection(string $collection): array
    {
        $entities = $this->findBy(['collection' => $collection], ['sortOrder' => 'ASC', 'createdAt' => 'ASC']);
        return array_map($this->mapper->toDomain(...), $entities);
    }

    /**
     * Returns a paginated slice of all field records.
     *
     * @param  int         $limit  Maximum records to return.
     * @param  int         $offset Zero-based pagination offset.
     * @return FieldMeta[]         Paginated field records.
     */
    public function findPaginated(int $limit, int $offset): array
    {
        $entities = $this->findBy([], ['collection' => 'ASC', 'sortOrder' => 'ASC'], $limit, $offset);
        return array_map($this->mapper->toDomain(...), $entities);
    }

    /**
     * Returns the total number of field records across all collections.
     *
     * @return int Total count.
     */
    public function countAll(): int
    {
        return $this->count([]);
    }

    /**
     * Deletes all field records belonging to the given collection via DQL bulk DELETE.
     *
     * @param string $collection Collection name whose fields should be removed.
     */
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
