<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Source;

use App\Portals\Domain\ValueObject\SourceType;

final class SourceAdapterRegistry
{
    /** @param iterable<SourceAdapterInterface> $adapters */
    public function __construct(private readonly iterable $adapters) {}

    public function get(SourceType $type): SourceAdapterInterface
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter instanceof AbstractSourceAdapter && $adapter->supports($type)) {
                return $adapter;
            }
        }

        throw new \InvalidArgumentException(
            sprintf('No source adapter registered for type "%s".', $type->value)
        );
    }
}
