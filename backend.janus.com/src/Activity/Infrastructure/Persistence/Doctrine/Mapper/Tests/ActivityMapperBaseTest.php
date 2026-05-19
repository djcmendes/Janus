<?php

/**
 * @file ActivityMapperBaseTest.php
 *
 * Constructor and interface compliance tests for ActivityMapper.
 *
 * @package App\Activity\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Activity\Infrastructure\Persistence\Doctrine\Mapper\ActivityMapper;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Test class for constructor and interface compliance tests for ActivityMapper.class.
 */
#[CoversClass(className:  ActivityMapper::class)]
final class ActivityMapperBaseTest extends ActivityMapperTest
{
    /**
     * Test that ActivityEntity can be instantiated with no arguments.
     */
    public function testIsInstanceOfActivityMapper(): void
    {
        $this->assertInstanceOf(expected: ActivityMapper::class, actual: $this->class);
    }
}
