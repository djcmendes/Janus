<?php

declare(strict_types=1);

namespace App\Fields\Presentation\DTO\Tests;

use App\Fields\Application\Command\UpdateFieldCommand;
use App\Fields\Presentation\DTO\UpdateFieldRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: UpdateFieldRequest::class)]
final class UpdateFieldRequestTest extends TestCase
{
    public function testFromArrayWithEmptyDataSetsUnchangedSentinels(): void
    {
        $req = UpdateFieldRequest::fromArray([]);

        $this->assertSame(UpdateFieldCommand::UNCHANGED, $req->label);
        $this->assertSame(UpdateFieldCommand::UNCHANGED, $req->note);
        $this->assertSame(UpdateFieldCommand::UNCHANGED, $req->interface);
        $this->assertSame(UpdateFieldCommand::UNCHANGED, $req->options);
    }

    public function testFromArrayWithEmptyDataSetsNullForBoolAndInt(): void
    {
        $req = UpdateFieldRequest::fromArray([]);

        $this->assertNull($req->required);
        $this->assertNull($req->readonly);
        $this->assertNull($req->hidden);
        $this->assertNull($req->sortOrder);
    }

    public function testFromArrayParsesLabel(): void
    {
        $req = UpdateFieldRequest::fromArray(['label' => 'New Label']);

        $this->assertSame('New Label', $req->label);
    }

    public function testFromArrayParsesNullLabel(): void
    {
        $req = UpdateFieldRequest::fromArray(['label' => null]);

        $this->assertNull($req->label);
    }

    public function testFromArrayParsesRequired(): void
    {
        $req = UpdateFieldRequest::fromArray(['required' => true]);

        $this->assertTrue($req->required);
    }

    public function testFromArrayParsesSortOrder(): void
    {
        $req = UpdateFieldRequest::fromArray(['sort' => 5]);

        $this->assertSame(5, $req->sortOrder);
    }

    public function testFromArrayParsesInterface(): void
    {
        $req = UpdateFieldRequest::fromArray(['interface' => 'input-text']);

        $this->assertSame('input-text', $req->interface);
    }

    public function testFromArrayParsesOptions(): void
    {
        $req = UpdateFieldRequest::fromArray(['options' => ['key' => 'val']]);

        $this->assertSame(['key' => 'val'], $req->options);
    }

    public function testFromArrayParsesNullOptions(): void
    {
        $req = UpdateFieldRequest::fromArray(['options' => null]);

        $this->assertNull($req->options);
    }
}
