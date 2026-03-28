<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;

final class PauseMagnetCommand
{
    public function __construct(public readonly string $magnetId) {}
}
