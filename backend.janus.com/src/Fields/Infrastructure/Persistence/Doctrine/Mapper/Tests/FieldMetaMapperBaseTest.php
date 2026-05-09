<?php

declare(strict_types=1);

namespace App\Fields\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Fields\Infrastructure\Persistence\Doctrine\Mapper\FieldMetaMapper;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FieldMetaMapper::class)]
final class FieldMetaMapperBaseTest extends FieldMetaMapperTest
{
    public function testMapperInstantiates(): void
    {
        $this->assertInstanceOf(FieldMetaMapper::class, $this->mapper);
    }
}
