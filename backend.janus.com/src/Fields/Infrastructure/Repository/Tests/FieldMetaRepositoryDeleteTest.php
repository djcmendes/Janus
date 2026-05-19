<?php

declare(strict_types=1);

namespace App\Fields\Infrastructure\Repository\Tests;

use App\Fields\Infrastructure\Repository\FieldMetaRepository;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: FieldMetaRepository::class)]
final class FieldMetaRepositoryDeleteTest extends FieldMetaRepositoryTest
{
    public function testDeleteRemovesAndFlushes(): void
    {
        $field = $this->makeFieldMeta();

        $this->entityManager->expects($this->once())->method('remove');
        $this->entityManager->expects($this->once())->method('flush');

        $this->class->delete($field);
    }
}
