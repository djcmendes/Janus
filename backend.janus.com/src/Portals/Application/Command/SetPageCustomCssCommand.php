<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;

final class SetPageCustomCssCommand
{
    public function __construct(
        public readonly string  $pageId,
        public readonly ?string $css,
    ) {}
}
