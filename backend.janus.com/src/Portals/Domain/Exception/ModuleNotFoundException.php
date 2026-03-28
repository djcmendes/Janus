<?php
declare(strict_types=1);
namespace App\Portals\Domain\Exception;
final class ModuleNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct("Module '{$id}' not found.");
    }
}
