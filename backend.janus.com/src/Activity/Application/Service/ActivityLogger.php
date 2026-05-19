<?php

/**
 * @file ActivityLogger.php
 *
 * Application service for recording audit-log entries from anywhere in the application.
 *
 * @package App\Activity\Application\Service
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Service;

use App\Activity\Domain\Entity\Activity;
use App\Activity\Domain\Repository\ActivityRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Convenience service for recording activity log entries.
 * Inject this wherever an action should be audited.
 */
final readonly class ActivityLogger
{
    /**
     * Constructor
     *
     * @param ActivityRepositoryInterface $repository   Repository used to persist activity records.
     * @param RequestStack                $requestStack Symfony request stack for resolving the current request's IP and User-Agent.
     */
    public function __construct(
        private ActivityRepositoryInterface $repository,
        private RequestStack                $requestStack,
    ) {}

    /**
     * Creates and persists an Activity entry, automatically capturing IP and User-Agent
     * from the current request when available.
     *
     * @param string      $action     Action type (e.g. 'create', 'update', 'delete').
     * @param string|null $collection Collection the action was performed on, or null.
     * @param string|null $item       Identifier of the affected item, or null.
     * @param string|null $userId     UUID of the user who performed the action, or null.
     * @return void
     */
    public function log(
        string  $action,
        ?string $collection = null,
        ?string $item       = null,
        ?string $userId     = null,
    ): void {
        $activity = new Activity(action: $action, collection: $collection, item: $item);
        $activity->setUserId(userId: $userId);

        $request = $this->requestStack->getCurrentRequest();
        if ($request !== null) {
            $activity->ip        = $request->getClientIp();
            $activity->userAgent = $request->headers->get(key: 'User-Agent');
        }

        $this->repository->record(activity: $activity);
    }
}
