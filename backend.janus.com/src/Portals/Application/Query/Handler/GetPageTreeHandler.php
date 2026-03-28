<?php
declare(strict_types=1);
namespace App\Portals\Application\Query\Handler;
use App\Portals\Application\DTO\PageTreeNodeDto;
use App\Portals\Application\Query\GetPageTreeQuery;
use App\Portals\Domain\Entity\Page;
use App\Portals\Domain\Repository\PageRepositoryInterface;
final class GetPageTreeHandler
{
    public function __construct(
        private readonly PageRepositoryInterface $repository,
    ) {}
    /** @return PageTreeNodeDto[] */
    public function handle(GetPageTreeQuery $query): array
    {
        $all = $this->repository->findByPortalId($query->portalId);
        return $this->buildTree($all, null);
    }
    /** @param Page[] $pages */
    private function buildTree(array $pages, ?string $parentId): array
    {
        $nodes = [];
        foreach ($pages as $page) {
            if ($page->getParentId() === $parentId) {
                $children = $this->buildTree($pages, $page->getId());
                $nodes[]  = PageTreeNodeDto::fromEntity($page, $children);
            }
        }
        usort($nodes, fn ($a, $b) => $a->sortOrder <=> $b->sortOrder);
        return $nodes;
    }
}
