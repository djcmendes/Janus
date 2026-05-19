<?php

declare(strict_types=1);

namespace App\Fields\Domain\Entity\Tests;

use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Enum\FieldType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: FieldMeta::class)]
final class FieldMetaBaseTest extends FieldMetaTest
{
    public function testIdIsUuidV7Format(): void
    {
        $f = $this->makeFieldMeta();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $f->getId(),
        );
    }

    public function testTwoInstancesHaveDifferentIds(): void
    {
        $a = $this->makeFieldMeta();
        $b = $this->makeFieldMeta();

        $this->assertNotSame($a->getId(), $b->getId());
    }

    public function testCollectionIsStored(): void
    {
        $f = $this->makeFieldMeta(collection: 'posts');

        $this->assertSame('posts', $f->getCollection());
    }

    public function testFieldNameIsStored(): void
    {
        $f = $this->makeFieldMeta(field: 'body');

        $this->assertSame('body', $f->getField());
    }

    public function testTypeIsStored(): void
    {
        $f = $this->makeFieldMeta(type: FieldType::TEXT);

        $this->assertSame(FieldType::TEXT, $f->getType());
    }

    public function testDefaultRequiredIsFalse(): void
    {
        $f = $this->makeFieldMeta();

        $this->assertFalse($f->isRequired());
    }

    public function testDefaultReadonlyIsFalse(): void
    {
        $f = $this->makeFieldMeta();

        $this->assertFalse($f->isReadonly());
    }

    public function testDefaultHiddenIsFalse(): void
    {
        $f = $this->makeFieldMeta();

        $this->assertFalse($f->isHidden());
    }

    public function testDefaultSortOrderIsZero(): void
    {
        $f = $this->makeFieldMeta();

        $this->assertSame(0, $f->getSortOrder());
    }

    public function testDefaultLabelIsNull(): void
    {
        $f = $this->makeFieldMeta();

        $this->assertNull($f->getLabel());
    }

    public function testDefaultNoteIsNull(): void
    {
        $f = $this->makeFieldMeta();

        $this->assertNull($f->getNote());
    }

    public function testDefaultInterfaceIsNull(): void
    {
        $f = $this->makeFieldMeta();

        $this->assertNull($f->getInterface());
    }

    public function testDefaultOptionsIsNull(): void
    {
        $f = $this->makeFieldMeta();

        $this->assertNull($f->getOptions());
    }

    public function testCreatedAtIsSetOnConstruction(): void
    {
        $before = new \DateTimeImmutable();
        $f      = $this->makeFieldMeta();
        $after  = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $f->getCreatedAt());
        $this->assertLessThanOrEqual($after, $f->getCreatedAt());
    }

    public function testUpdatedAtIsNullByDefault(): void
    {
        $f = $this->makeFieldMeta();

        $this->assertNull($f->getUpdatedAt());
    }
}
