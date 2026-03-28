<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;
final class UpdateLayoutTemplateCommand
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly array  $positions,
        public readonly string $templateMarkup,
    ) {}
}
