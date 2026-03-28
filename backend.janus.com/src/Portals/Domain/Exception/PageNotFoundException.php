<?php
declare(strict_types=1);
namespace App\Portals\Domain\Exception;
final class PageNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct("Page '{$id}' not found.");
    }
}
