<?php

declare(strict_types=1);

namespace App\Fields\Application\Command\Tests;

use App\Fields\Application\Command\DeleteFieldCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: DeleteFieldCommand::class)]
final class DeleteFieldCommandTest extends TestCase
{
    public function testStoresCollection(): void
    {
        $cmd = new DeleteFieldCommand('articles', 'title');

        $this->assertSame('articles', $cmd->collection);
    }

    public function testStoresField(): void
    {
        $cmd = new DeleteFieldCommand('articles', 'title');

        $this->assertSame('title', $cmd->field);
    }
}
