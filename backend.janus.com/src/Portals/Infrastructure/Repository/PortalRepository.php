<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Repository;
use App\Portals\Domain\Entity\Portal;
use App\Portals\Domain\Repository\PortalRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Portal> */
final class PortalRepository extends ServiceEntityRepository implements PortalRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Portal::class);
    }

    public function save(Portal $portal, bool $flush = true): void
    {
        $this->getEntityManager()->persist($portal);
        if ($flush) { $this->getEntityManager()->flush(); }
    }

    public function delete(Portal $portal): void
    {
        $this->getEntityManager()->remove($portal);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Portal
    {
        return $this->find($id);
    }

    public function findByBaseRoute(string $route): ?Portal
    {
        return $this->findOneBy(['baseRoute' => $route]);
    }

    /** @return Portal[] */
    public function findPaginated(int $limit, int $offset): array
    {
        return $this->findBy([], ['createdAt' => 'ASC'], $limit, $offset);
    }

    public function countAll(): int
    {
        return $this->count([]);
    }
}
