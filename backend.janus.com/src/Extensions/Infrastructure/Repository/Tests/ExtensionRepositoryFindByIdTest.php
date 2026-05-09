<?php

declare(strict_types=1);

namespace App\Extensions\Infrastructure\Repository\Tests;

use App\Extensions\Domain\Entity\Extension;
use App\Extensions\Infrastructure\Repository\ExtensionRepository;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ExtensionRepository::class)]
final class ExtensionRepositoryFindByIdTest extends ExtensionRepositoryTest
{
    public function testFindByIdReturnsMappedDomainEntity(): void
    {
        $entity = $this->makeExtensionEntity();

        $this->entityManager->method('find')->willReturn($entity);

        $result = $this->class->findById('aaaaaaaa-0000-7000-8000-000000000001');

        $this->assertInstanceOf(Extension::class, $result);
        $this->assertSame('my-hook', $result->getName());
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $this->entityManager->method('find')->willReturn(null);

        $result = $this->class->findById('nonexistent-id');

        $this->assertNull($result);
    }
}
