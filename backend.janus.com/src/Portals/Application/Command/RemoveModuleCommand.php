<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;
final class RemoveModuleCommand
{
    public function __construct(public readonly string $placementId) {}
}
