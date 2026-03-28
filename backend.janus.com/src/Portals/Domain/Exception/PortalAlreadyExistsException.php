<?php
declare(strict_types=1);
namespace App\Portals\Domain\Exception;
final class PortalAlreadyExistsException extends \RuntimeException
{
    public function __construct(string $route)
    {
        parent::__construct("A portal with base route '{$route}' already exists.");
    }
}
