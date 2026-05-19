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

/**
 * Test class for constructor and interface compliance tests for ActivityEntity.class.
 */
#[CoversClass(className:  ActivityEntity::class)]
final class ActivityEntityBaseTest extends ActivityEntityTest
{
    /**
     * Test that ActivityEntity Fluent Setter Chain Populates all fields
     */
    public function testFluentSetterChainPopulatesAllFields(): void
    {
        $entity = $this->makeEntity();

        $this->assertSame(expected: 'create', actual: $entity->action);
        $this->assertSame(expected: 'posts', actual: $entity->collection);
        $this->assertSame(expected: '42', actual: $entity->item);
        $this->assertSame(expected: 'bbbbbbbb-0000-7000-8000-000000000002', actual: $entity->userId);
        $this->assertSame(expected: '127.0.0.1', actual: $entity->ip);
        $this->assertSame(expected: 'PHPUnit', actual: $entity->userAgent);
    }
}
