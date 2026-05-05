<?php

/**
 * @file ActivityDtoBaseTest.php
 *
 * Constructor and interface compliance tests for ActivityDto.
 *
 * @package App\Activity\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\DTO\Tests;

use App\Activity\Application\DTO\ActivityDto;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ActivityDto::class)]
final class ActivityDtoBaseTest extends ActivityDtoTest
{
    public function testIsInstanceOfActivityDto(): void
    {
        $this->assertInstanceOf(ActivityDto::class, $this->class);
    }

    public function testIdPropertyIsReadonly(): void
    {
        $this->assertTrue($this->reflection->getProperty('id')->isReadOnly());
    }

    public function testActionPropertyIsReadonly(): void
    {
        $this->assertTrue($this->reflection->getProperty('action')->isReadOnly());
    }

    public function testCollectionPropertyIsReadonly(): void
    {
        $this->assertTrue($this->reflection->getProperty('collection')->isReadOnly());
    }

    public function testItemPropertyIsReadonly(): void
    {
        $this->assertTrue($this->reflection->getProperty('item')->isReadOnly());
    }

    public function testUserIdPropertyIsReadonly(): void
    {
        $this->assertTrue($this->reflection->getProperty('userId')->isReadOnly());
    }

    public function testIpPropertyIsReadonly(): void
    {
        $this->assertTrue($this->reflection->getProperty('ip')->isReadOnly());
    }

    public function testUserAgentPropertyIsReadonly(): void
    {
        $this->assertTrue($this->reflection->getProperty('userAgent')->isReadOnly());
    }

    public function testTimestampPropertyIsReadonly(): void
    {
        $this->assertTrue($this->reflection->getProperty('timestamp')->isReadOnly());
    }
}
