<?php
declare(strict_types=1);
namespace App\Portals\Domain\Exception;
final class PortalNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct("Portal '{$id}' not found.");
    }
}
