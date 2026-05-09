<?php

declare(strict_types=1);

namespace App\Fields\Application\Command\Tests;

use App\Fields\Application\Command\CreateFieldCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CreateFieldCommand::class)]
final class CreateFieldCommandTest extends TestCase
{
    public function testStoresCollection(): void
    {
        $cmd = new CreateFieldCommand('articles', 'title', 'string');

        $this->assertSame('articles', $cmd->collection);
    }

    public function testStoresField(): void
    {
        $cmd = new CreateFieldCommand('articles', 'title', 'string');

        $this->assertSame('title', $cmd->field);
    }

    public function testStoresType(): void
    {
        $cmd = new CreateFieldCommand('articles', 'title', 'string');

        $this->assertSame('string', $cmd->type);
    }

    public function testDefaultLabelIsNull(): void
    {
        $cmd = new CreateFieldCommand('articles', 'title', 'string');

        $this->assertNull($cmd->label);
    }

    public function testDefaultRequiredIsFalse(): void
    {
        $cmd = new CreateFieldCommand('articles', 'title', 'string');

        $this->assertFalse($cmd->required);
    }

    public function testDefaultSortOrderIsZero(): void
    {
        $cmd = new CreateFieldCommand('articles', 'title', 'string');

        $this->assertSame(0, $cmd->sortOrder);
    }

    public function testOptionalFieldsCanBeProvided(): void
    {
        $cmd = new CreateFieldCommand(
            collection: 'posts',
            field:      'body',
            type:       'text',
            label:      'Body Text',
            note:       'Rich text content',
            required:   true,
            readonly:   true,
            hidden:     true,
            sortOrder:  5,
            interface:  'input-rich-text',
            options:    ['toolbar' => 'full'],
        );

        $this->assertSame('Body Text', $cmd->label);
        $this->assertTrue($cmd->required);
        $this->assertSame(5, $cmd->sortOrder);
        $this->assertSame(['toolbar' => 'full'], $cmd->options);
    }
}
