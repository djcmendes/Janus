<?php

declare(strict_types=1);

namespace App\Extensions\Infrastructure\Repository\Tests;

use App\Extensions\Infrastructure\Repository\ExtensionRepository;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ExtensionRepository::class)]
final class ExtensionRepositoryCountAllTest extends ExtensionRepositoryTest
{
    public function testCountAllReturnsInt(): void
    {
        $this->query->method('getSingleScalarResult')->willReturn(42);

        $result = $this->class->countAll();

        $this->assertSame(42, $result);
    }

    public function testCountAllAppliesTypeFilter(): void
    {
        $this->queryBuilder->expects($this->atLeastOnce())->method('andWhere');
        $this->query->method('getSingleScalarResult')->willReturn(5);

        $this->class->countAll(type: 'module');
    }

    public function testCountAllAppliesEnabledFilter(): void
    {
        $this->queryBuilder->expects($this->atLeastOnce())->method('andWhere');
        $this->query->method('getSingleScalarResult')->willReturn(3);

        $this->class->countAll(enabled: false);
    }

    public function testCountAllNoFilterWhenParamsAreNull(): void
    {
        $this->queryBuilder->expects($this->never())->method('andWhere');
        $this->query->method('getSingleScalarResult')->willReturn(0);

        $this->class->countAll(type: null, enabled: null);
    }
}
