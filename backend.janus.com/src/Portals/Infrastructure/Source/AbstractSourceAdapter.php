<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Source;

use App\Portals\Domain\ValueObject\SourceType;

abstract class AbstractSourceAdapter implements SourceAdapterInterface
{
    abstract public function supports(SourceType $type): bool;
}
