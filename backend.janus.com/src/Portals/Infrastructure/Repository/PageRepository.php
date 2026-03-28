<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Repository;
use App\Portals\Domain\Entity\Page;
use App\Portals\Domain\Repository\PageRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<Page> */
final class PageRepository extends ServiceEntityRepository implements PageRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }
    public function save(Page $page, bool $flush = true): void
    {
        $this->getEntityManager()->persist($page);
        if ($flush) { $this->getEntityManager()->flush(); }
    }
    public function delete(Page $page): void
    {
        $this->getEntityManager()->remove($page);
        $this->getEntityManager()->flush();
    }
    public function findById(string $id): ?Page { return $this->find($id); }
    /** @return Page[] */
    public function findByPortalId(string $portalId): array
    {
        return $this->findBy(['portalId' => $portalId], ['sortOrder' => 'ASC', 'createdAt' => 'ASC']);
    }
    /** @return Page[] */
    public function findChildren(string $parentId): array
    {
        return $this->findBy(['parentId' => $parentId], ['sortOrder' => 'ASC']);
    }
    public function countByPortal(string $portalId): int
    {
        return $this->count(['portalId' => $portalId]);
    }
}
