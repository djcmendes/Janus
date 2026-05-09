<?php

declare(strict_types=1);

namespace App\Extensions\Domain\Entity\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use App\Extensions\Domain\Entity\Extension;

#[CoversClass(Extension::class)]
final class ExtensionSetMetaTest extends ExtensionTest
{
    public function testSetMetaStoresValue(): void
    {
        $ext  = $this->makeExtension();
        $meta = ['entry' => 'dist/index.js', 'icon' => 'plug'];
        $ext->setMeta($meta);

        $this->assertSame($meta, $ext->getMeta());
    }

    public function testSetMetaAcceptsNull(): void
    {
        $ext = $this->makeExtension(meta: ['entry' => 'index.js']);
        $ext->setMeta(null);

        $this->assertNull($ext->getMeta());
    }

    public function testSetMetaRefreshesUpdatedAt(): void
    {
        $ext    = $this->makeExtension();
        $before = $ext->getUpdatedAt();

        usleep(1000);
        $ext->setMeta(['entry' => 'index.js']);

        $this->assertGreaterThan($before, $ext->getUpdatedAt());
    }

    public function testSetMetaReturnsFluent(): void
    {
        $ext = $this->makeExtension();

        $this->assertSame($ext, $ext->setMeta(['k' => 'v']));
    }
}
