<?php

declare(strict_types=1);

namespace App\Fields\Infrastructure\Repository\Tests;

use App\Fields\Infrastructure\Repository\FieldMetaRepository;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: FieldMetaRepository::class)]
final class FieldMetaRepositoryCountAllTest extends FieldMetaRepositoryTest
{
    public function testCountAllReturnsInt(): void
    {
        $this->query->method('getSingleScalarResult')->willReturn(42);
        $this->queryBuilder->method('select')->willReturn($this->queryBuilder);

        $result = $this->class->countAll();

        $this->assertIsInt($result);
    }
}
