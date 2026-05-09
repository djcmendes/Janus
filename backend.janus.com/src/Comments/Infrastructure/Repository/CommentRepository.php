<?php

/**
 * @file CommentRepository.php
 *
 * Doctrine ORM implementation of CommentRepositoryInterface.
 * Bridges the domain layer and the database via CommentMapper and CommentEntity.
 *
 * @package App\Comments\Infrastructure\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Infrastructure\Repository;

use App\Comments\Domain\Entity\Comment;
use App\Comments\Domain\Repository\CommentRepositoryInterface;
use App\Comments\Infrastructure\Persistence\Doctrine\Entity\CommentEntity;
use App\Comments\Infrastructure\Persistence\Doctrine\Mapper\CommentMapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine ORM repository for Comment records.
 *
 * Persists and retrieves CommentEntity models via Doctrine, using CommentMapper
 * to convert between the Doctrine model and the pure Comment domain entity.
 * All queries are ordered by createdAt descending so callers receive the most
 * recent entries first without needing to specify sorting.
 *
 * @extends ServiceEntityRepository<CommentEntity>
 * @implements CommentRepositoryInterface
 */
final class CommentRepository extends ServiceEntityRepository implements CommentRepositoryInterface
{
    /**
     * @param ManagerRegistry $registry Doctrine's entity manager registry.
     * @param CommentMapper   $mapper   Converts between domain Comment and Doctrine CommentEntity.
     */
    public function __construct(
        ManagerRegistry                $registry,
        private readonly CommentMapper $mapper,
    ) {
        parent::__construct($registry, CommentEntity::class);
    }

    /**
     * Persists a Comment record to the database.
     *
     * @param Comment $comment The domain entity to store.
     * @param bool    $flush   Whether to flush the entity manager immediately.
     *
     * @return void
     */
    public function save(Comment $comment, bool $flush = true): void
    {
        $entity = $this->mapper->toPersistence($comment);

        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Removes a Comment record from the database by locating its managed entity.
     *
     * @param Comment $comment The domain entity whose persisted record should be deleted.
     *
     * @return void
     */
    public function delete(Comment $comment): void
    {
        $entity = $this->find($comment->getId());

        if ($entity !== null) {
            $this->getEntityManager()->remove($entity);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Finds a single Comment domain entity by its UUID primary key.
     *
     * @param string $id The UUID of the comment record to retrieve.
     *
     * @return Comment|null The matching domain entity, or null if no record exists.
     */
    public function findById(string $id): ?Comment
    {
        $entity = $this->find($id);

        return $entity !== null ? $this->mapper->toDomain($entity) : null;
    }

    /**
     * Returns a page of Comment domain entities, ordered by createdAt descending,
     * with optional filters applied.
     *
     * @param int         $limit      Maximum number of records to return.
     * @param int         $offset     Number of records to skip (pagination offset).
     * @param string|null $collection Filter to comments on a specific collection name, or null to include all.
     * @param string|null $item       Filter to comments on a specific item identifier, or null to include all.
     *
     * @return array<Comment> Ordered array of domain Comment entities matching the given criteria.
     */
    public function findPaginated(
        int     $limit,
        int     $offset,
        ?string $collection = null,
        ?string $item       = null,
    ): array {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($collection !== null) {
            $qb->andWhere('c.collection = :collection')->setParameter('collection', $collection);
        }
        if ($item !== null) {
            $qb->andWhere('c.item = :item')->setParameter('item', $item);
        }

        return array_map(
            $this->mapper->toDomain(...),
            $qb->getQuery()->getResult(),
        );
    }

    /**
     * Returns the total count of Comment records matching the given filters,
     * used to populate the `total_count` meta field in paginated responses.
     *
     * @param string|null $collection Filter to comments on a specific collection name, or null for all.
     * @param string|null $item       Filter to comments on a specific item identifier, or null for all.
     *
     * @return int Total number of matching records.
     */
    public function countAll(
        ?string $collection = null,
        ?string $item       = null,
    ): int {
        $qb = $this->createQueryBuilder('c')->select('COUNT(c.id)');

        if ($collection !== null) {
            $qb->andWhere('c.collection = :collection')->setParameter('collection', $collection);
        }
        if ($item !== null) {
            $qb->andWhere('c.item = :item')->setParameter('item', $item);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
