<?php

declare(strict_types=1);

namespace App\Fields\Domain\Entity\Tests;

use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Enum\FieldType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: FieldMeta::class)]
final class FieldMetaSettersTest extends FieldMetaTest
{
    public function testSetTypeUpdatesValue(): void
    {
        $f = $this->makeFieldMeta();
        $f->setType(FieldType::INTEGER);

        $this->assertSame(FieldType::INTEGER, $f->getType());
    }

    public function testSetTypeRefreshesUpdatedAt(): void
    {
        $f = $this->makeFieldMeta();
        $f->setType(FieldType::BOOLEAN);

        $this->assertNotNull($f->getUpdatedAt());
    }

    public function testSetLabelUpdatesValue(): void
    {
        $f = $this->makeFieldMeta();
        $f->setLabel('My Label');

        $this->assertSame('My Label', $f->getLabel());
    }

    public function testSetLabelToNullClearsValue(): void
    {
        $f = $this->makeFieldMeta();
        $f->setLabel(null);

        $this->assertNull($f->getLabel());
    }

    public function testSetLabelRefreshesUpdatedAt(): void
    {
        $f = $this->makeFieldMeta();
        $f->setLabel('Label');

        $this->assertNotNull($f->getUpdatedAt());
    }

    public function testSetNoteUpdatesValue(): void
    {
        $f = $this->makeFieldMeta();
        $f->setNote('A description note');

        $this->assertSame('A description note', $f->getNote());
    }

    public function testSetNoteRefreshesUpdatedAt(): void
    {
        $f = $this->makeFieldMeta();
        $f->setNote('note');

        $this->assertNotNull($f->getUpdatedAt());
    }

    public function testSetRequiredToTrue(): void
    {
        $f = $this->makeFieldMeta();
        $f->setRequired(true);

        $this->assertTrue($f->isRequired());
    }

    public function testSetRequiredRefreshesUpdatedAt(): void
    {
        $f = $this->makeFieldMeta();
        $f->setRequired(true);

        $this->assertNotNull($f->getUpdatedAt());
    }

    public function testSetReadonlyToTrue(): void
    {
        $f = $this->makeFieldMeta();
        $f->setReadonly(true);

        $this->assertTrue($f->isReadonly());
    }

    public function testSetReadonlyRefreshesUpdatedAt(): void
    {
        $f = $this->makeFieldMeta();
        $f->setReadonly(true);

        $this->assertNotNull($f->getUpdatedAt());
    }

    public function testSetHiddenToTrue(): void
    {
        $f = $this->makeFieldMeta();
        $f->setHidden(true);

        $this->assertTrue($f->isHidden());
    }

    public function testSetHiddenRefreshesUpdatedAt(): void
    {
        $f = $this->makeFieldMeta();
        $f->setHidden(true);

        $this->assertNotNull($f->getUpdatedAt());
    }

    public function testSetSortOrderUpdatesValue(): void
    {
        $f = $this->makeFieldMeta();
        $f->setSortOrder(10);

        $this->assertSame(10, $f->getSortOrder());
    }

    public function testSetSortOrderRefreshesUpdatedAt(): void
    {
        $f = $this->makeFieldMeta();
        $f->setSortOrder(3);

        $this->assertNotNull($f->getUpdatedAt());
    }

    public function testSetInterfaceUpdatesValue(): void
    {
        $f = $this->makeFieldMeta();
        $f->setInterface('input-text');

        $this->assertSame('input-text', $f->getInterface());
    }

    public function testSetInterfaceToNullClearsValue(): void
    {
        $f = $this->makeFieldMeta();
        $f->setInterface(null);

        $this->assertNull($f->getInterface());
    }

    public function testSetInterfaceRefreshesUpdatedAt(): void
    {
        $f = $this->makeFieldMeta();
        $f->setInterface('slider');

        $this->assertNotNull($f->getUpdatedAt());
    }

    public function testSetOptionsUpdatesValue(): void
    {
        $f = $this->makeFieldMeta();
        $f->setOptions(['min' => 0, 'max' => 100]);

        $this->assertSame(['min' => 0, 'max' => 100], $f->getOptions());
    }

    public function testSetOptionsToNullClearsValue(): void
    {
        $f = $this->makeFieldMeta();
        $f->setOptions(null);

        $this->assertNull($f->getOptions());
    }

    public function testSetOptionsRefreshesUpdatedAt(): void
    {
        $f = $this->makeFieldMeta();
        $f->setOptions(['key' => 'val']);

        $this->assertNotNull($f->getUpdatedAt());
    }

    public function testSetterReturnsSelf(): void
    {
        $f = $this->makeFieldMeta();

        $this->assertSame($f, $f->setLabel('test'));
    }
}
