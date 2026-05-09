<?php

declare(strict_types=1);

namespace App\Fields\Application\Command\Tests;

use App\Fields\Application\Command\UpdateFieldCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UpdateFieldCommand::class)]
final class UpdateFieldCommandTest extends TestCase
{
    public function testStoresCollection(): void
    {
        $cmd = new UpdateFieldCommand('articles', 'title');

        $this->assertSame('articles', $cmd->collection);
    }

    public function testStoresField(): void
    {
        $cmd = new UpdateFieldCommand('articles', 'title');

        $this->assertSame('title', $cmd->field);
    }

    public function testDefaultLabelIsUnchanged(): void
    {
        $cmd = new UpdateFieldCommand('articles', 'title');

        $this->assertSame(UpdateFieldCommand::UNCHANGED, $cmd->label);
    }

    public function testDefaultNoteIsUnchanged(): void
    {
        $cmd = new UpdateFieldCommand('articles', 'title');

        $this->assertSame(UpdateFieldCommand::UNCHANGED, $cmd->note);
    }

    public function testDefaultRequiredIsNull(): void
    {
        $cmd = new UpdateFieldCommand('articles', 'title');

        $this->assertNull($cmd->required);
    }

    public function testDefaultReadonlyIsNull(): void
    {
        $cmd = new UpdateFieldCommand('articles', 'title');

        $this->assertNull($cmd->readonly);
    }

    public function testDefaultHiddenIsNull(): void
    {
        $cmd = new UpdateFieldCommand('articles', 'title');

        $this->assertNull($cmd->hidden);
    }

    public function testDefaultSortOrderIsNull(): void
    {
        $cmd = new UpdateFieldCommand('articles', 'title');

        $this->assertNull($cmd->sortOrder);
    }

    public function testDefaultInterfaceIsUnchanged(): void
    {
        $cmd = new UpdateFieldCommand('articles', 'title');

        $this->assertSame(UpdateFieldCommand::UNCHANGED, $cmd->interface);
    }

    public function testDefaultOptionsIsUnchanged(): void
    {
        $cmd = new UpdateFieldCommand('articles', 'title');

        $this->assertSame(UpdateFieldCommand::UNCHANGED, $cmd->options);
    }

    public function testUnchangedSentinelValue(): void
    {
        $this->assertSame('__UNCHANGED__', UpdateFieldCommand::UNCHANGED);
    }

    public function testProvidedValuesAreStored(): void
    {
        $cmd = new UpdateFieldCommand('articles', 'title', label: 'New Label', required: true);

        $this->assertSame('New Label', $cmd->label);
        $this->assertTrue($cmd->required);
    }
}
