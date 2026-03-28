<?php
declare(strict_types=1);
namespace App\Portals\Domain\Exception;

final class MagnetNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Magnet "%s" not found.', $id));
    }
}
