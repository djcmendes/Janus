<?php

declare(strict_types=1);

namespace App\Fields\Infrastructure\Repository\Tests;

use App\Fields\Infrastructure\Repository\FieldMetaRepository;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: FieldMetaRepository::class)]
final class FieldMetaRepositoryBaseTest extends FieldMetaRepositoryTest
{
    public function testRepositoryInstantiates(): void
    {
        $this->assertInstanceOf(FieldMetaRepository::class, $this->class);
    }

    public function testRepositoryHasSaveMethod(): void
    {
        $this->assertTrue($this->reflection->hasMethod('save'));
    }

    public function testRepositoryHasDeleteMethod(): void
    {
        $this->assertTrue($this->reflection->hasMethod('delete'));
    }

    public function testRepositoryHasFindByCollectionAndFieldMethod(): void
    {
        $this->assertTrue($this->reflection->hasMethod('findByCollectionAndField'));
    }

    public function testRepositoryHasFindByCollectionMethod(): void
    {
        $this->assertTrue($this->reflection->hasMethod('findByCollection'));
    }

    public function testRepositoryHasFindPaginatedMethod(): void
    {
        $this->assertTrue($this->reflection->hasMethod('findPaginated'));
    }

    public function testRepositoryHasCountAllMethod(): void
    {
        $this->assertTrue($this->reflection->hasMethod('countAll'));
    }

    public function testRepositoryHasDeleteByCollectionMethod(): void
    {
        $this->assertTrue($this->reflection->hasMethod('deleteByCollection'));
    }
}
