<?php

declare(strict_types=1);

namespace App\Fields\Infrastructure\Repository\Tests;

use App\Fields\Infrastructure\Repository\FieldMetaRepository;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: FieldMetaRepository::class)]
final class FieldMetaRepositorySaveTest extends FieldMetaRepositoryTest
{
    public function testSavePersistsAndFlushes(): void
    {
        $field = $this->makeFieldMeta();

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->class->save($field);
    }

    public function testSaveWithFlushFalseDoesNotFlush(): void
    {
        $field = $this->makeFieldMeta();

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->never())->method('flush');

        $this->class->save($field, false);
    }
}
