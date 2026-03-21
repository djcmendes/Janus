<?php

declare(strict_types=1);

namespace App\Presets\Infrastructure\Repository;

use App\Presets\Domain\Entity\Preset;
use App\Presets\Domain\Repository\PresetRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Preset>
 */
final class PresetRepository extends ServiceEntityRepository implements PresetRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Preset::class);
    }

    public function save(Preset $preset): void
    {
        $this->getEntityManager()->persist($preset);
        $this->getEntityManager()->flush();
    }

    public function delete(Preset $preset): void
    {
        $this->getEntityManager()->remove($preset);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Preset
    {
        return $this->find($id);
    }

    /** @return Preset[] */
    public function findPaginated(
        int     $limit,
        int     $offset,
        ?string $collection = null,
        ?string $userId     = null,
    ): array {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($collection !== null) {
            $qb->andWhere('p.collection = :collection')->setParameter('collection', $collection);
        }
        if ($userId !== null) {
            $qb->andWhere('p.userId = :userId')->setParameter('userId', $userId);
        }

        return $qb->getQuery()->getResult();
    }

    public function countAll(
        ?string $collection = null,
        ?string $userId     = null,
    ): int {
        $qb = $this->createQueryBuilder('p')->select('COUNT(p.id)');

        if ($collection !== null) {
            $qb->andWhere('p.collection = :collection')->setParameter('collection', $collection);
        }
        if ($userId !== null) {
            $qb->andWhere('p.userId = :userId')->setParameter('userId', $userId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
