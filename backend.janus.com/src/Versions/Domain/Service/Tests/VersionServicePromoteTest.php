<?php

/**
 * @file VersionServicePromoteTest.php
 *
 * Tests for VersionService::promote().
 *
 * @package App\Versions\Domain\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Domain\Service\Tests;

use App\Versions\Domain\Entity\Version;
use App\Versions\Domain\Service\VersionService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies promote() executes the UPDATE statement, handles edge cases, and throws on failure.
 */
#[CoversClass(VersionService::class)]
#[CoversMethod(VersionService::class, 'promote')]
final class VersionServicePromoteTest extends VersionServiceTest
{
    /**
     * Test that promote() calls executeStatement() on the connection exactly once.
     */
    public function testPromoteCallsExecuteStatementOnce(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('executeStatement')
            ->willReturn(1);

        $this->class->promote($this->makeVersion());
    }

    /**
     * Test that promote() throws RuntimeException when the item row is not found (0 rows affected).
     */
    public function testPromoteThrowsWhenItemNotFound(): void
    {
        $this->connection
            ->method('executeStatement')
            ->willReturn(0);

        $this->expectException(\RuntimeException::class);

        $this->class->promote($this->makeVersion());
    }

    /**
     * Test that promote() does nothing when data contains only an 'id' key (nothing to SET).
     */
    public function testPromoteDoesNothingForEmptyDataAfterIdRemoval(): void
    {
        $this->connection
            ->expects($this->never())
            ->method('executeStatement');

        $version = new Version('articles', 'item-uuid-1', 'main', ['id' => 'some-id']);

        $this->class->promote($version);
    }

    /**
     * Test that promote() throws InvalidArgumentException for an invalid collection identifier.
     */
    public function testPromoteThrowsForInvalidCollectionName(): void
    {
        $version = new Version('invalid table; DROP TABLE versions;', 'item-1', 'main', ['title' => 'x']);

        $this->expectException(\InvalidArgumentException::class);

        $this->class->promote($version);
    }
}
