<?php

declare(strict_types=1);

namespace App\Revisions\Domain\Service\tests;

use App\Revisions\Domain\Repository\RevisionRepositoryInterface;
use App\Revisions\Domain\Service\RevisionRecorder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class RevisionRecorderTestCase extends TestCase
{
    protected RevisionRecorder $recorder;
    protected RevisionRepositoryInterface&MockObject $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(RevisionRepositoryInterface::class);
        $this->recorder   = new RevisionRecorder($this->repository);
    }

    protected function tearDown(): void
    {
        unset($this->recorder, $this->repository);
    }
}
