<?php
declare(strict_types=1);
namespace App\Portals\Domain\Exception;
final class ComponentNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct("Component '{$id}' not found.");
    }
}
