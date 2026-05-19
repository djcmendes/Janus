<?php

declare(strict_types=1);

namespace App\Fields\Presentation\DTO\Tests;

use App\Fields\Presentation\DTO\CreateFieldRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: CreateFieldRequest::class)]
final class CreateFieldRequestTest extends TestCase
{
    public function testFromArrayParsesRequiredFields(): void
    {
        $req = CreateFieldRequest::fromArray(['field' => 'title', 'type' => 'string']);

        $this->assertSame('title', $req->field);
        $this->assertSame('string', $req->type);
    }

    public function testFromArrayThrowsWhenFieldMissing(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CreateFieldRequest::fromArray(['type' => 'string']);
    }

    public function testFromArrayThrowsWhenTypeMissing(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CreateFieldRequest::fromArray(['field' => 'title']);
    }

    public function testFromArrayThrowsOnInvalidFieldName(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CreateFieldRequest::fromArray(['field' => '1invalid', 'type' => 'string']);
    }

    public function testFromArrayThrowsOnInvalidType(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CreateFieldRequest::fromArray(['field' => 'title', 'type' => 'nonexistent-type']);
    }

    public function testFromArraySetsDefaultsCorrectly(): void
    {
        $req = CreateFieldRequest::fromArray(['field' => 'title', 'type' => 'string']);

        $this->assertFalse($req->required);
        $this->assertFalse($req->readonly);
        $this->assertFalse($req->hidden);
        $this->assertSame(0, $req->sortOrder);
        $this->assertNull($req->label);
        $this->assertNull($req->note);
        $this->assertNull($req->interface);
        $this->assertNull($req->options);
    }

    public function testFromArrayParsesOptionalFields(): void
    {
        $req = CreateFieldRequest::fromArray([
            'field'     => 'body',
            'type'      => 'text',
            'label'     => 'Body Text',
            'note'      => 'Rich text',
            'required'  => true,
            'readonly'  => true,
            'hidden'    => true,
            'sort'      => 3,
            'interface' => 'input-rich-text',
            'options'   => ['toolbar' => 'full'],
        ]);

        $this->assertSame('Body Text', $req->label);
        $this->assertTrue($req->required);
        $this->assertSame(3, $req->sortOrder);
        $this->assertSame('input-rich-text', $req->interface);
        $this->assertSame(['toolbar' => 'full'], $req->options);
    }

    public function testFromArrayAcceptsAliasType(): void
    {
        $req = CreateFieldRequest::fromArray(['field' => 'virtual_field', 'type' => 'alias']);

        $this->assertSame('alias', $req->type);
    }
}
