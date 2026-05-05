<?php

/**
 * @file ActivityEntitySetActionTest.php
 *
 * Tests for ActivityEntity::setAction() and ActivityEntity::getAction().
 *
 * @package App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(ActivityEntity::class)]
#[CoversMethod(ActivityEntity::class, 'setAction')]
final class ActivityEntitySetActionTest extends ActivityEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetActionStoresValue(): void
    {
        $this->class->setAction('delete');

        $this->assertSame('delete', $this->class->getAction());
    }

    public function testSetActionReturnsStaticInstance(): void
    {
        $result = $this->class->setAction('delete');

        $this->assertSame($this->class, $result);
    }
}
