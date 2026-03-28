<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\CreatePageCommand;
use App\Portals\Application\DTO\PageDto;
use App\Portals\Domain\Entity\Page;
use App\Portals\Domain\Exception\PageNotFoundException;
use App\Portals\Domain\Repository\PageRepositoryInterface;
use App\Portals\Domain\ValueObject\Slug;
final class CreatePageHandler
{
    public function __construct(
        private readonly PageRepositoryInterface $repository,
    ) {}
    public function handle(CreatePageCommand $command): PageDto
    {
        $slug     = new Slug($command->slug);
        $fullPath = $this->computeFullPath($command->parentId, $slug->toString());
        $page     = new Page(
            portalId: $command->portalId,
            title:    $command->title,
            slug:     $slug,
            fullPath: $fullPath,
            parentId: $command->parentId,
        );
        if ($command->layoutTemplateId !== null) {
            $page->setLayoutTemplate($command->layoutTemplateId);
        }
        $page->setSortOrder($command->sortOrder);
        $this->repository->save($page);
        return PageDto::fromEntity($page);
    }
    private function computeFullPath(?string $parentId, string $slug): string
    {
        if ($parentId === null) {
            return '/' . $slug;
        }
        $parent = $this->repository->findById($parentId);
        if ($parent === null) {
            throw new PageNotFoundException($parentId);
        }
        return rtrim($parent->getFullPath(), '/') . '/' . $slug;
    }
}
