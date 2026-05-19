<?php

declare(strict_types=1);

namespace App\Fields\Infrastructure\Repository\Tests;

use App\Fields\Infrastructure\Repository\FieldMetaRepository;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: FieldMetaRepository::class)]
final class FieldMetaRepositoryFindByCollectionAndFieldTest extends FieldMetaRepositoryTest
{
    public function testFindByCollectionAndFieldReturnsNullWhenNotFound(): void
    {
        $this->query->method('getOneOrNullResult')->willReturn(null);

        $result = $this->class->findByCollectionAndField('nonexistent', 'field');

        $this->assertNull($result);
    }
}
