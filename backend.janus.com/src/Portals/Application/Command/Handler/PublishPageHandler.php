<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\PublishPageCommand;
use App\Portals\Application\DTO\PageDto;
use App\Portals\Domain\Exception\PageNotFoundException;
use App\Portals\Domain\Repository\PageRepositoryInterface;
final class PublishPageHandler
{
    public function __construct(private readonly PageRepositoryInterface $repository) {}
    public function handle(PublishPageCommand $command): PageDto
    {
        $page = $this->repository->findById($command->id);
        if ($page === null) { throw new PageNotFoundException($command->id); }
        $page->publish();
        $this->repository->save($page);
        return PageDto::fromEntity($page);
    }
}
