<?php

declare(strict_types=1);

namespace App\Panels\Infrastructure\Repository;

use App\Panels\Domain\Entity\Panel;
use App\Panels\Domain\Repository\PanelRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Panel>
 */
final class PanelRepository extends ServiceEntityRepository implements PanelRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Panel::class);
    }

    public function save(Panel $panel): void
    {
        $this->getEntityManager()->persist($panel);
        $this->getEntityManager()->flush();
    }

    public function delete(Panel $panel): void
    {
        $this->getEntityManager()->remove($panel);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Panel
    {
        return $this->find($id);
    }

    /** @return Panel[] */
    public function findPaginated(int $limit, int $offset, ?string $dashboardId = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.positionY', 'ASC')
            ->addOrderBy('p.positionX', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($dashboardId !== null) {
            $qb->andWhere('p.dashboardId = :dashboardId')->setParameter('dashboardId', $dashboardId);
        }

        return $qb->getQuery()->getResult();
    }

    public function countAll(?string $dashboardId = null): int
    {
        $qb = $this->createQueryBuilder('p')->select('COUNT(p.id)');

        if ($dashboardId !== null) {
            $qb->andWhere('p.dashboardId = :dashboardId')->setParameter('dashboardId', $dashboardId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function deleteByDashboard(string $dashboardId): void
    {
        $this->createQueryBuilder('p')
            ->delete()
            ->where('p.dashboardId = :dashboardId')
            ->setParameter('dashboardId', $dashboardId)
            ->getQuery()
            ->execute();
    }
}
