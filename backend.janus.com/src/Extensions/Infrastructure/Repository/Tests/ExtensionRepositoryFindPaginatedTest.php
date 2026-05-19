<?php

declare(strict_types=1);

namespace App\Extensions\Infrastructure\Repository\Tests;

use App\Extensions\Domain\Entity\Extension;
use App\Extensions\Infrastructure\Repository\ExtensionRepository;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: ExtensionRepository::class)]
final class ExtensionRepositoryFindPaginatedTest extends ExtensionRepositoryTest
{
    public function testFindPaginatedReturnsArrayOfDomainEntities(): void
    {
        $this->query->method('getResult')->willReturn([$this->makeExtensionEntity()]);

        $results = $this->class->findPaginated(10, 0);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertInstanceOf(Extension::class, $results[0]);
    }

    public function testFindPaginatedReturnsEmptyWhenNoResults(): void
    {
        $this->query->method('getResult')->willReturn([]);

        $results = $this->class->findPaginated(10, 0);

        $this->assertSame([], $results);
    }

    public function testFindPaginatedAppliesTypeFilter(): void
    {
        $this->queryBuilder->expects($this->atLeastOnce())->method('andWhere');

        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(10, 0, type: 'hook');
    }

    public function testFindPaginatedAppliesEnabledFilter(): void
    {
        $this->queryBuilder->expects($this->atLeastOnce())->method('andWhere');

        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(10, 0, enabled: true);
    }

    public function testFindPaginatedNoFilterWhenParamsAreNull(): void
    {
        $this->queryBuilder->expects($this->never())->method('andWhere');

        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(10, 0, type: null, enabled: null);
    }
}
