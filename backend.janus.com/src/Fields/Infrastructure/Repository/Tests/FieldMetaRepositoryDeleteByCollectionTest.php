<?php

declare(strict_types=1);

namespace App\Fields\Infrastructure\Repository\Tests;

use App\Fields\Infrastructure\Repository\FieldMetaRepository;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FieldMetaRepository::class)]
final class FieldMetaRepositoryDeleteByCollectionTest extends FieldMetaRepositoryTest
{
    public function testDeleteByCollectionExecutesQuery(): void
    {
        $this->query->expects($this->once())->method('execute');

        $this->class->deleteByCollection('articles');
    }
}
