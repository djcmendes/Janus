<?php

/**
 * @file ActivityEntitySetActionTest.php
 *
 * Tests for ActivityEntity::setAction() and ActivityEntity::action { get; set }.
 *
 * @package App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Test class for ActivityEntity::setAction() — storing and retrieving the action.
 */
#[CoversClass(className:  ActivityEntity::class)]
#[CoversMethod(className: ActivityEntity::class, methodName: 'setAction')]
final class ActivityEntitySetActionTest extends ActivityEntityTest
{

    /**
     * Test that setAction() stores the values.
     */
    public function testSetActionStoresValue(): void
    {
        $this->class->setAction(action: 'delete');

        $this->assertSame(expected: 'delete', actual: $this->class->action);
    }

    /**
     * Test that setAction() returns static instance
     */
    public function testSetActionReturnsStaticInstance(): void
    {
        $result = $this->class->setAction(action: 'delete');

        $this->assertSame(expected: $this->class, actual: $result);
    }
}
