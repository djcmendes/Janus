<?php

declare(strict_types=1);

namespace App\Extensions\Domain\Entity\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use App\Extensions\Domain\Entity\Extension;

#[CoversClass(Extension::class)]
final class ExtensionSetVersionTest extends ExtensionTest
{
    public function testSetVersionStoresValue(): void
    {
        $ext = $this->makeExtension(version: '1.0.0');
        $ext->setVersion('2.5.1');

        $this->assertSame('2.5.1', $ext->getVersion());
    }

    public function testSetVersionRefreshesUpdatedAt(): void
    {
        $ext    = $this->makeExtension();
        $before = $ext->getUpdatedAt();

        usleep(1000);
        $ext->setVersion('9.9.9');

        $this->assertGreaterThan($before, $ext->getUpdatedAt());
    }

    public function testSetVersionReturnsFluent(): void
    {
        $ext = $this->makeExtension();

        $this->assertSame($ext, $ext->setVersion('1.2.3'));
    }
}
