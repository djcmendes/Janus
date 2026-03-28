<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;
final class DeleteModuleCommand
{
    public function __construct(public readonly string $id) {}
}
