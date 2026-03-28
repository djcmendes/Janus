<?php
declare(strict_types=1);
namespace App\Portals\Domain\Exception;
final class PlacementNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct("ModulePlacement '{$id}' not found.");
    }
}
