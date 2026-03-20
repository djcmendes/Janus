<?php

declare(strict_types=1);

namespace App\Revisions\Domain\Service;

use App\Revisions\Domain\Entity\Revision;
use App\Revisions\Domain\Repository\RevisionRepositoryInterface;

/**
 * Records item snapshots as revisions.
 * Inject this into any handler that mutates item data.
 *
 * Usage:
 *   $this->revisionRecorder->record('posts', $itemId, $newData, $activityId);
 */
final class RevisionRecorder
{
    public function __construct(
        private readonly RevisionRepositoryInterface $repository,
    ) {}

    /**
     * Creates a new revision for the given item.
     *
     * @param string      $collection   The collection (table) name
     * @param string      $item         The item UUID as string
     * @param array       $data         Full current state of the item
     * @param string|null $activityId   UUID of the linked activity log entry
     */
    public function record(
        string  $collection,
        string  $item,
        array   $data,
        ?string $activityId = null,
    ): void {
        $previous = $this->repository->findLatestForItem($collection, $item);
        $version  = $previous !== null ? $previous->getVersion() + 1 : 1;
        $delta    = $previous !== null ? $this->computeDelta($previous->getData(), $data) : null;

        $revision = new Revision($collection, $item, $data, $version, $activityId);
        $revision->setDelta($delta);

        $this->repository->record($revision);
    }

    /**
     * Returns only the keys whose values differ between old and new data.
     */
    private function computeDelta(array $old, array $new): array
    {
        $delta = [];

        foreach ($new as $key => $value) {
            if (!array_key_exists($key, $old) || $old[$key] !== $value) {
                $delta[$key] = $value;
            }
        }

        return $delta;
    }
}
