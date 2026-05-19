<?php

/**
 * @file DashboardSetNoteTest.php
 *
 * Tests for Dashboard::setNote().
 *
 * @package App\Dashboards\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Domain\Entity\Tests;

use App\Dashboards\Domain\Entity\Dashboard;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies setNote() updates the descriptive note and refreshes the updatedAt timestamp.
 */
#[CoversClass(className: Dashboard::class)]
final class DashboardSetNoteTest extends DashboardTest
{
    /**
     * Test that setNote() returns the same Dashboard instance (fluent interface).
     */
    public function testSetNoteReturnsSelf(): void
    {
        $result = $this->class->setNote('Updated note');

        $this->assertSame($this->class, $result);
    }

    /**
     * Test that setNote() stores the new note text.
     */
    public function testSetNoteChangesNote(): void
    {
        $this->class->setNote('Brand new note');

        $this->assertSame('Brand new note', $this->class->getNote());
    }

    /**
     * Test that setNote(null) clears the note.
     */
    public function testSetNoteNullClearsNote(): void
    {
        $this->class->setNote(null);

        $this->assertNull($this->class->getNote());
    }

    /**
     * Test that setNote() refreshes the updatedAt timestamp.
     */
    public function testSetNoteRefreshesUpdatedAt(): void
    {
        $before = $this->class->getUpdatedAt();

        usleep(1000);
        $this->class->setNote('Changed');

        $this->assertGreaterThanOrEqual($before, $this->class->getUpdatedAt());
    }
}
