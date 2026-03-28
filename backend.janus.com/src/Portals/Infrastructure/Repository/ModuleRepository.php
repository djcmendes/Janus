<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Repository;
use App\Portals\Domain\Entity\Module;
use App\Portals\Domain\Repository\ModuleRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<Module> */
final class ModuleRepository extends ServiceEntityRepository implements ModuleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }
    public function save(Module $module, bool $flush = true): void
    {
        $this->getEntityManager()->persist($module);
        if ($flush) { $this->getEntityManager()->flush(); }
    }
    public function delete(Module $module): void
    {
        $this->getEntityManager()->remove($module);
        $this->getEntityManager()->flush();
    }
    public function findById(string $id): ?Module { return $this->find($id); }
    /** @return Module[] */
    public function findPaginated(int $limit, int $offset, ?string $portalId = null): array
    {
        $criteria = $portalId !== null ? ['portalId' => $portalId] : [];
        return $this->findBy($criteria, ['createdAt' => 'ASC'], $limit, $offset);
    }
    public function countAll(?string $portalId = null): int
    {
        $criteria = $portalId !== null ? ['portalId' => $portalId] : [];
        return $this->count($criteria);
    }
}
