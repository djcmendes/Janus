<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;
final class CreateLayoutTemplateCommand
{
    public function __construct(
        public readonly string $name,
        public readonly array  $positions,
        public readonly string $templateMarkup,
    ) {}
}
