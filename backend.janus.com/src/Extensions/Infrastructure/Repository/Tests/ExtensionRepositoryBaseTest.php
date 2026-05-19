<?php

declare(strict_types=1);

namespace App\Extensions\Infrastructure\Repository\Tests;

use App\Extensions\Infrastructure\Repository\ExtensionRepository;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: ExtensionRepository::class)]
final class ExtensionRepositoryBaseTest extends ExtensionRepositoryTest
{
    public function testRepositoryCanBeInstantiated(): void
    {
        $this->assertInstanceOf(ExtensionRepository::class, $this->class);
    }
}
