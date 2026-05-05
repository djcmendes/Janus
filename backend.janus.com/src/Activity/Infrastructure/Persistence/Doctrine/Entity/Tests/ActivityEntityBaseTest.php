<?php

/**
 * @file ActivityEntityBaseTest.php
 *
 * Constructor and interface compliance tests for ActivityEntity.
 *
 * @package App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ActivityEntity::class)]
final class ActivityEntityBaseTest extends ActivityEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testIsInstantiableWithNoArguments(): void
    {
        $this->assertInstanceOf(ActivityEntity::class, $this->class);
    }

    public function testFluentSetterChainPopulatesAllFields(): void
    {
        $entity = $this->makeEntity();

        $this->assertSame('create', $entity->getAction());
        $this->assertSame('posts', $entity->getCollection());
        $this->assertSame('42', $entity->getItem());
        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $entity->getUserId());
        $this->assertSame('127.0.0.1', $entity->getIp());
        $this->assertSame('PHPUnit', $entity->getUserAgent());
    }
}
