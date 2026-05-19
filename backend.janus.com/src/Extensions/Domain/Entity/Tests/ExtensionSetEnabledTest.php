<?php

declare(strict_types=1);

namespace App\Extensions\Domain\Entity\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use App\Extensions\Domain\Entity\Extension;

#[CoversClass(className: Extension::class)]
final class ExtensionSetEnabledTest extends ExtensionTest
{
    public function testSetEnabledToTrue(): void
    {
        $ext = $this->makeExtension(enabled: false);
        $ext->setEnabled(true);

        $this->assertTrue($ext->isEnabled());
    }

    public function testSetEnabledToFalse(): void
    {
        $ext = $this->makeExtension(enabled: true);
        $ext->setEnabled(false);

        $this->assertFalse($ext->isEnabled());
    }

    public function testSetEnabledRefreshesUpdatedAt(): void
    {
        $ext    = $this->makeExtension();
        $before = $ext->getUpdatedAt();

        usleep(1000);
        $ext->setEnabled(true);

        $this->assertGreaterThan($before, $ext->getUpdatedAt());
    }

    public function testSetEnabledReturnsFluent(): void
    {
        $ext = $this->makeExtension();

        $this->assertSame($ext, $ext->setEnabled(true));
    }
}
