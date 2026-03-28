<?php
declare(strict_types=1);
namespace App\Portals\Domain\Exception;
final class LayoutTemplateNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct("LayoutTemplate '{$id}' not found.");
    }
}
