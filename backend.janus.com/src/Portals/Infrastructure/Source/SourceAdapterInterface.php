<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Source;

use App\Portals\Domain\Entity\Magnet;

interface SourceAdapterInterface
{
    /**
     * Executes the import and returns the number of items imported.
     *
     * @throws \RuntimeException on unrecoverable errors
     */
    public function import(Magnet $magnet): int;
}
