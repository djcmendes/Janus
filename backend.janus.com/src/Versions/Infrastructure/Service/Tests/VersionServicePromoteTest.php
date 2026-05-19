<?php

/**
 * @file VersionServicePromoteTest.php
 *
 * Tests for VersionService::promote().
 *
 * @package App\Versions\Infrastructure\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Service\Tests;

use App\Versions\Domain\Entity\Version;
use App\Versions\Infrastructure\Service\VersionService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies promote() executes the UPDATE statement, handles edge cases, and throws on failure.
 */
#[CoversClass(className: VersionService::class)]
#[CoversMethod(VersionService::class, 'promote')]
final class VersionServicePromoteTest extends VersionServiceTest
{
    public function testPromoteCallsExecuteStatementOnce(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('executeStatement')
            ->willReturn(1);

        $this->class->promote($this->makeVersion());
    }

    public function testPromoteThrowsWhenItemNotFound(): void
    {
        $this->connection
            ->method('executeStatement')
            ->willReturn(0);

        $this->expectException(\RuntimeException::class);

        $this->class->promote($this->makeVersion());
    }

    public function testPromoteDoesNothingForEmptyDataAfterIdRemoval(): void
    {
        $this->connection
            ->expects($this->never())
            ->method('executeStatement');

        $version = new Version('articles', 'item-uuid-1', 'main', ['id' => 'some-id']);

        $this->class->promote($version);
    }

    public function testPromoteThrowsForInvalidCollectionName(): void
    {
        $version = new Version('invalid table; DROP TABLE versions;', 'item-1', 'main', ['title' => 'x']);

        $this->expectException(\InvalidArgumentException::class);

        $this->class->promote($version);
    }
}
