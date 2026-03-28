<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Repository;

use App\Portals\Domain\Entity\MagnetRun;
use App\Portals\Domain\Repository\MagnetRunRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<MagnetRun> */
final class MagnetRunRepository extends ServiceEntityRepository implements MagnetRunRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MagnetRun::class);
    }

    public function save(MagnetRun $run, bool $flush = true): void
    {
        $this->getEntityManager()->persist($run);
        if ($flush) { $this->getEntityManager()->flush(); }
    }

    public function findById(string $id): ?MagnetRun { return $this->find($id); }

    /** @return MagnetRun[] */
    public function findByMagnetId(string $magnetId, int $limit = 25, int $offset = 0): array
    {
        return $this->findBy(['magnetId' => $magnetId], ['startedAt' => 'DESC'], $limit, $offset);
    }

    public function countByMagnetId(string $magnetId): int
    {
        return $this->count(['magnetId' => $magnetId]);
    }

    public function findLatestByMagnetId(string $magnetId): ?MagnetRun
    {
        return $this->findOneBy(['magnetId' => $magnetId], ['startedAt' => 'DESC']);
    }
}
