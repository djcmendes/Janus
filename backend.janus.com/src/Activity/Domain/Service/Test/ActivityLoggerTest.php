<?php

declare(strict_types=1);

namespace App\Activity\Domain\Service\Test;

use App\Activity\Domain\Entity\Activity;
use App\Activity\Domain\Repository\ActivityRepositoryInterface;
use App\Activity\Domain\Service\ActivityLogger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class ActivityLoggerTest extends TestCase
{
    private ActivityRepositoryInterface $repository;
    private RequestStack $requestStack;
    private ActivityLogger $logger;

    protected function setUp(): void
    {
        $this->repository   = $this->createMock(ActivityRepositoryInterface::class);
        $this->requestStack = new RequestStack();
        $this->logger       = new ActivityLogger($this->repository, $this->requestStack);
    }

    // ── log ───────────────────────────────────────────────────────────────

    public function testLogRecordsActivityWithRequiredFields(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('record')
            ->with($this->callback(function (Activity $a): bool {
                return $a->getAction()     === 'create'
                    && $a->getCollection() === 'articles'
                    && $a->getItem()       === 'item-1';
            }));

        $this->logger->log('create', 'articles', 'item-1');
    }

    public function testLogSetsUserIdOnActivity(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('record')
            ->with($this->callback(function (Activity $a): bool {
                return $a->getUserId() === 'user-uuid-123';
            }));

        $this->logger->log('update', 'articles', 'item-1', 'user-uuid-123');
    }

    public function testLogCapturesIpAndUserAgentFromRequest(): void
    {
        $request = Request::create('/', 'GET', [], [], [], [
            'REMOTE_ADDR'     => '192.168.1.100',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 Test Browser',
        ]);
        $this->requestStack->push($request);

        $this->repository
            ->expects($this->once())
            ->method('record')
            ->with($this->callback(function (Activity $a): bool {
                return $a->getIp()        === '192.168.1.100'
                    && $a->getUserAgent() === 'Mozilla/5.0 Test Browser';
            }));

        $this->logger->log('read', 'articles', 'item-1');
    }

    public function testLogWorksWithoutActiveRequest(): void
    {
        // No request pushed onto the stack
        $this->repository
            ->expects($this->once())
            ->method('record')
            ->with($this->callback(function (Activity $a): bool {
                return $a->getIp()        === null
                    && $a->getUserAgent() === null;
            }));

        $this->logger->log('delete', 'articles', 'item-1');
    }

    public function testLogAcceptsNullCollectionAndItem(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('record')
            ->with($this->callback(function (Activity $a): bool {
                return $a->getAction()     === 'login'
                    && $a->getCollection() === null
                    && $a->getItem()       === null;
            }));

        $this->logger->log('login');
    }
}
