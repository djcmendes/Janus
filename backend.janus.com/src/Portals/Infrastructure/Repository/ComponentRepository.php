<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Repository;
use App\Portals\Domain\Entity\ComponentDefinition;
use App\Portals\Domain\Repository\ComponentRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<ComponentDefinition> */
final class ComponentRepository extends ServiceEntityRepository implements ComponentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ComponentDefinition::class);
    }
    public function save(ComponentDefinition $component, bool $flush = true): void
    {
        $this->getEntityManager()->persist($component);
        if ($flush) { $this->getEntityManager()->flush(); }
    }
    public function delete(ComponentDefinition $component): void
    {
        $this->getEntityManager()->remove($component);
        $this->getEntityManager()->flush();
    }
    public function findById(string $id): ?ComponentDefinition { return $this->find($id); }
    /** @return ComponentDefinition[] */
    public function findPaginated(int $limit, int $offset): array
    {
        return $this->findBy([], ['createdAt' => 'ASC'], $limit, $offset);
    }
    public function countAll(): int { return $this->count([]); }
}
