<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\UpdateLayoutTemplateCommand;
use App\Portals\Application\DTO\LayoutTemplateDto;
use App\Portals\Domain\Exception\LayoutTemplateNotFoundException;
use App\Portals\Domain\Repository\LayoutTemplateRepositoryInterface;
use App\Portals\Domain\ValueObject\Position;
final class UpdateLayoutTemplateHandler
{
    public function __construct(
        private readonly LayoutTemplateRepositoryInterface $repository,
    ) {}
    public function handle(UpdateLayoutTemplateCommand $command): LayoutTemplateDto
    {
        $template = $this->repository->findById($command->id);
        if ($template === null) { throw new LayoutTemplateNotFoundException($command->id); }
        $positions = array_map(fn ($p) => Position::fromArray($p), $command->positions);
        $template->update($command->name, $positions, $command->templateMarkup);
        $this->repository->save($template);
        return LayoutTemplateDto::fromEntity($template);
    }
}
