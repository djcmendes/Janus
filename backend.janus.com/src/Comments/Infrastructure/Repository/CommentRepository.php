<?php

declare(strict_types=1);

namespace App\Comments\Infrastructure\Repository;

use App\Comments\Domain\Entity\Comment;
use App\Comments\Domain\Repository\CommentRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
final class CommentRepository extends ServiceEntityRepository implements CommentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function save(Comment $comment): void
    {
        $this->getEntityManager()->persist($comment);
        $this->getEntityManager()->flush();
    }

    public function delete(Comment $comment): void
    {
        $this->getEntityManager()->remove($comment);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Comment
    {
        return $this->find($id);
    }

    /** @return Comment[] */
    public function findAll(
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

        return $qb->getQuery()->getResult();
    }

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
