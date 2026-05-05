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

#[CoversClass(ActivityMapper::class)]
final class ActivityMapperBaseTest extends ActivityMapperTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testIsInstanceOfActivityMapper(): void
    {
        $this->assertInstanceOf(ActivityMapper::class, $this->class);
    }
}
