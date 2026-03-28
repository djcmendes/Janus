<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;

use App\Portals\Application\Command\SetPageCustomCssCommand;
use App\Portals\Application\DTO\PageDto;
use App\Portals\Domain\Exception\PageNotFoundException;
use App\Portals\Domain\Repository\PageRepositoryInterface;

final class SetPageCustomCssHandler
{
    public function __construct(
        private readonly PageRepositoryInterface $repository,
    ) {}

    public function handle(SetPageCustomCssCommand $command): PageDto
    {
        $page = $this->repository->findById($command->pageId);
        if ($page === null) {
            throw new PageNotFoundException($command->pageId);
        }

        $page->setCustomCss($command->css);
        $this->repository->save($page);

        return PageDto::fromEntity($page);
    }
}
