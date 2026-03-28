<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\AssignCenterComponentCommand;
use App\Portals\Application\DTO\PageDto;
use App\Portals\Domain\Exception\PageNotFoundException;
use App\Portals\Domain\Repository\PageRepositoryInterface;
final class AssignCenterComponentHandler
{
    public function __construct(private readonly PageRepositoryInterface $repository) {}
    public function handle(AssignCenterComponentCommand $command): PageDto
    {
        $page = $this->repository->findById($command->pageId);
        if ($page === null) { throw new PageNotFoundException($command->pageId); }
        $page->setCenterComponent($command->componentId);
        $this->repository->save($page);
        return PageDto::fromEntity($page);
    }
}
