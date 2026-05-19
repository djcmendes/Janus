<?php

declare(strict_types=1);

namespace App\Extensions\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Extensions\Infrastructure\Persistence\Doctrine\Mapper\ExtensionMapper;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: ExtensionMapper::class)]
final class ExtensionMapperBaseTest extends ExtensionMapperTest
{
    public function testMapperCanBeInstantiated(): void
    {
        $this->assertInstanceOf(ExtensionMapper::class, $this->mapper);
    }
}
