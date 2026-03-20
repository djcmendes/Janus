<?php

declare(strict_types=1);

namespace App\Shares\Infrastructure\Repository;

use App\Shares\Domain\Entity\Share;
use App\Shares\Domain\Repository\ShareRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Share>
 */
final class ShareRepository extends ServiceEntityRepository implements ShareRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Share::class);
    }

    public function save(Share $share): void
    {
        $this->getEntityManager()->persist($share);
        $this->getEntityManager()->flush();
    }

    public function delete(Share $share): void
    {
        $this->getEntityManager()->remove($share);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Share
    {
        return $this->find($id);
    }

    public function findByToken(string $token): ?Share
    {
        return $this->findOneBy(['token' => $token]);
    }

    /** @return Share[] */
    public function findAll(
        int     $limit,
        int     $offset,
        ?string $collection = null,
        ?string $userId     = null,
    ): array {
        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($collection !== null) {
            $qb->andWhere('s.collection = :collection')->setParameter('collection', $collection);
        }
        if ($userId !== null) {
            $qb->andWhere('s.userId = :userId')->setParameter('userId', $userId);
        }

        return $qb->getQuery()->getResult();
    }

    public function countAll(
        ?string $collection = null,
        ?string $userId     = null,
    ): int {
        $qb = $this->createQueryBuilder('s')->select('COUNT(s.id)');

        if ($collection !== null) {
            $qb->andWhere('s.collection = :collection')->setParameter('collection', $collection);
        }
        if ($userId !== null) {
            $qb->andWhere('s.userId = :userId')->setParameter('userId', $userId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
