<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Repository;

use App\Portals\Domain\Entity\Magnet;
use App\Portals\Domain\Repository\MagnetRepositoryInterface;
use App\Portals\Domain\ValueObject\MagnetStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Magnet> */
final class MagnetRepository extends ServiceEntityRepository implements MagnetRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Magnet::class);
    }

    public function save(Magnet $magnet, bool $flush = true): void
    {
        $this->getEntityManager()->persist($magnet);
        if ($flush) { $this->getEntityManager()->flush(); }
    }

    public function delete(Magnet $magnet): void
    {
        $this->getEntityManager()->remove($magnet);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Magnet { return $this->find($id); }

    /** @return Magnet[] */
    public function findByPortalId(string $portalId, int $limit = 25, int $offset = 0): array
    {
        return $this->findBy(
            ['portalId' => $portalId],
            ['createdAt' => 'ASC'],
            $limit,
            $offset
        );
    }

    public function countByPortalId(string $portalId): int
    {
        return $this->count(['portalId' => $portalId]);
    }

    public function countActiveByPortalId(string $portalId): int
    {
        return $this->count(['portalId' => $portalId, 'status' => MagnetStatus::ACTIVE->value]);
    }

    /** @return Magnet[] */
    public function findActiveWithSchedule(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.status = :status')
            ->andWhere('m.schedule IS NOT NULL')
            ->setParameter('status', MagnetStatus::ACTIVE->value)
            ->getQuery()
            ->getResult();
    }
}
