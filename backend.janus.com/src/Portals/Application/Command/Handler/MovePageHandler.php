<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\MovePageCommand;
use App\Portals\Application\DTO\PageDto;
use App\Portals\Domain\Exception\PageNotFoundException;
use App\Portals\Domain\Repository\PageRepositoryInterface;
final class MovePageHandler
{
    public function __construct(
        private readonly PageRepositoryInterface $repository,
    ) {}
    public function handle(MovePageCommand $command): PageDto
    {
        $page = $this->repository->findById($command->id);
        if ($page === null) { throw new PageNotFoundException($command->id); }
        $oldFullPath = $page->getFullPath();
        $newFullPath = $this->resolveFullPath($command->parentId, $page->getSlug()->toString());
        $page->moveTo($command->parentId, $newFullPath);
        $this->repository->save($page, false);
        $this->updateSubtreePaths($page->getId(), $oldFullPath, $newFullPath);
        return PageDto::fromEntity($page);
    }
    private function resolveFullPath(?string $parentId, string $slug): string
    {
        if ($parentId === null) {
            return '/' . $slug;
        }
        $parent = $this->repository->findById($parentId);
        if ($parent === null) { throw new PageNotFoundException($parentId); }
        return rtrim($parent->getFullPath(), '/') . '/' . $slug;
    }
    private function updateSubtreePaths(string $pageId, string $oldPrefix, string $newPrefix): void
    {
        foreach ($this->repository->findChildren($pageId) as $child) {
            $updatedPath = $newPrefix . substr($child->getFullPath(), strlen($oldPrefix));
            $child->moveTo($child->getParentId(), $updatedPath);
            $this->repository->save($child, false);
            $this->updateSubtreePaths($child->getId(), $oldPrefix . '/' . $child->getSlug()->toString(), $updatedPath);
        }
    }
}
