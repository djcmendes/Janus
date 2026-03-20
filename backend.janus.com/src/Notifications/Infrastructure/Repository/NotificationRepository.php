<?php

declare(strict_types=1);

namespace App\Notifications\Infrastructure\Repository;

use App\Notifications\Domain\Entity\Notification;
use App\Notifications\Domain\Repository\NotificationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
final class NotificationRepository extends ServiceEntityRepository implements NotificationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function save(Notification $notification): void
    {
        $this->getEntityManager()->persist($notification);
        $this->getEntityManager()->flush();
    }

    public function delete(Notification $notification): void
    {
        $this->getEntityManager()->remove($notification);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Notification
    {
        return $this->find($id);
    }

    /** @return Notification[] */
    public function findAll(
        int     $limit,
        int     $offset,
        ?string $recipientId = null,
        ?bool   $read        = null,
    ): array {
        $qb = $this->createQueryBuilder('n')
            ->orderBy('n.timestamp', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($recipientId !== null) {
            $qb->andWhere('n.recipientId = :recipientId')->setParameter('recipientId', $recipientId);
        }
        if ($read !== null) {
            $qb->andWhere('n.read = :read')->setParameter('read', $read);
        }

        return $qb->getQuery()->getResult();
    }

    public function countAll(
        ?string $recipientId = null,
        ?bool   $read        = null,
    ): int {
        $qb = $this->createQueryBuilder('n')->select('COUNT(n.id)');

        if ($recipientId !== null) {
            $qb->andWhere('n.recipientId = :recipientId')->setParameter('recipientId', $recipientId);
        }
        if ($read !== null) {
            $qb->andWhere('n.read = :read')->setParameter('read', $read);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
