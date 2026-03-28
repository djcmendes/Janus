<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\CreateLayoutTemplateCommand;
use App\Portals\Application\DTO\LayoutTemplateDto;
use App\Portals\Domain\Entity\LayoutTemplate;
use App\Portals\Domain\Repository\LayoutTemplateRepositoryInterface;
use App\Portals\Domain\ValueObject\Position;
final class CreateLayoutTemplateHandler
{
    public function __construct(
        private readonly LayoutTemplateRepositoryInterface $repository,
    ) {}
    public function handle(CreateLayoutTemplateCommand $command): LayoutTemplateDto
    {
        $positions = array_map(fn ($p) => Position::fromArray($p), $command->positions);
        $template  = new LayoutTemplate($command->name, $positions, $command->templateMarkup);
        $this->repository->save($template);
        return LayoutTemplateDto::fromEntity($template);
    }
}
