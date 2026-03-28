<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Repository;
use App\Portals\Domain\Entity\ModulePlacement;
use App\Portals\Domain\Repository\ModulePlacementRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<ModulePlacement> */
final class ModulePlacementRepository extends ServiceEntityRepository implements ModulePlacementRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModulePlacement::class);
    }
    public function save(ModulePlacement $placement, bool $flush = true): void
    {
        $this->getEntityManager()->persist($placement);
        if ($flush) { $this->getEntityManager()->flush(); }
    }
    public function delete(ModulePlacement $placement): void
    {
        $this->getEntityManager()->remove($placement);
        $this->getEntityManager()->flush();
    }
    public function findById(string $id): ?ModulePlacement { return $this->find($id); }
    /** @return ModulePlacement[] */
    public function findByPage(string $pageId): array
    {
        return $this->findBy(['pageId' => $pageId], ['positionName' => 'ASC', 'sortOrder' => 'ASC']);
    }
    /** @return ModulePlacement[] */
    public function findByPageAndPosition(string $pageId, string $positionName): array
    {
        return $this->findBy(['pageId' => $pageId, 'positionName' => $positionName], ['sortOrder' => 'ASC']);
    }
}
