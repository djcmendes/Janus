<?php

declare(strict_types=1);

namespace App\Revisions\Domain\Service\tests;

use App\Revisions\Domain\Entity\Revision;

class RevisionRecorder_recordTest extends RevisionRecorderTestCase
{
    public function test_first_revision_has_version_one_and_null_delta(): void
    {
        $this->repository
            ->method('findLatestForItem')
            ->willReturn(null);

        $saved = null;
        $this->repository
            ->expects($this->once())
            ->method('record')
            ->willReturnCallback(function (Revision $r) use (&$saved): void {
                $saved = $r;
            });

        $this->recorder->record('posts', 'item-uuid', ['title' => 'Hello']);

        $this->assertSame(1, $saved->getVersion());
        $this->assertNull($saved->getDelta());
    }

    public function test_second_revision_increments_version(): void
    {
        $first = new Revision('posts', 'item-uuid', ['title' => 'Hello'], 1, null);

        $this->repository
            ->method('findLatestForItem')
            ->willReturn($first);

        $saved = null;
        $this->repository
            ->expects($this->once())
            ->method('record')
            ->willReturnCallback(function (Revision $r) use (&$saved): void {
                $saved = $r;
            });

        $this->recorder->record('posts', 'item-uuid', ['title' => 'World']);

        $this->assertSame(2, $saved->getVersion());
    }

    public function test_second_revision_delta_contains_only_changed_keys(): void
    {
        $first = new Revision('posts', 'item-uuid', ['title' => 'Hello', 'body' => 'Old'], 1, null);

        $this->repository
            ->method('findLatestForItem')
            ->willReturn($first);

        $saved = null;
        $this->repository
            ->expects($this->once())
            ->method('record')
            ->willReturnCallback(function (Revision $r) use (&$saved): void {
                $saved = $r;
            });

        $this->recorder->record('posts', 'item-uuid', ['title' => 'Changed', 'body' => 'Old']);

        $delta = $saved->getDelta();
        $this->assertArrayHasKey('title', $delta);
        $this->assertArrayNotHasKey('body', $delta);
    }
}
