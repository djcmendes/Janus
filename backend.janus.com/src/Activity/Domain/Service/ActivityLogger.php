<?php

declare(strict_types=1);

namespace App\Activity\Domain\Service;

use App\Activity\Domain\Entity\Activity;
use App\Activity\Domain\Repository\ActivityRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Convenience service for recording activity log entries.
 * Inject this wherever an action should be audited.
 */
final class ActivityLogger
{
    public function __construct(
        private readonly ActivityRepositoryInterface $repository,
        private readonly RequestStack               $requestStack,
    ) {}

    /**
     * Records an activity entry, automatically capturing IP and User-Agent
     * from the current request when available.
     */
    public function log(
        string  $action,
        ?string $collection = null,
        ?string $item       = null,
        ?string $userId     = null,
    ): void {
        $activity = new Activity($action, $collection, $item);
        $activity->setUserId($userId);

        $request = $this->requestStack->getCurrentRequest();
        if ($request !== null) {
            $activity->setIp($request->getClientIp());
            $activity->setUserAgent($request->headers->get('User-Agent'));
        }

        $this->repository->record($activity);
    }
}
