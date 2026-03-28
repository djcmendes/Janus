<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Repository;
use App\Portals\Domain\Entity\LayoutTemplate;
use App\Portals\Domain\Repository\LayoutTemplateRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<LayoutTemplate> */
final class LayoutTemplateRepository extends ServiceEntityRepository implements LayoutTemplateRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LayoutTemplate::class);
    }
    public function save(LayoutTemplate $template, bool $flush = true): void
    {
        $this->getEntityManager()->persist($template);
        if ($flush) { $this->getEntityManager()->flush(); }
    }
    public function delete(LayoutTemplate $template): void
    {
        $this->getEntityManager()->remove($template);
        $this->getEntityManager()->flush();
    }
    public function findById(string $id): ?LayoutTemplate { return $this->find($id); }
    /** @return LayoutTemplate[] */
    public function findPaginated(int $limit, int $offset): array
    {
        return $this->findBy([], ['createdAt' => 'ASC'], $limit, $offset);
    }
    public function countAll(): int { return $this->count([]); }
}
