<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\Handler\Test;

use App\Settings\Application\DTO\SettingsDto;
use App\Settings\Application\Query\GetSettingsQuery;
use App\Settings\Application\Query\Handler\GetSettingsHandler;
use App\Settings\Domain\Entity\Settings;
use App\Settings\Domain\Repository\SettingsRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class GetSettingsHandlerTest extends TestCase
{
    public function testHandleReturnsSettingsDto(): void
    {
        $settings = new Settings();

        $repository = $this->createMock(SettingsRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('getOrCreate')
            ->willReturn($settings);

        $handler = new GetSettingsHandler($repository);
        $dto     = $handler->handle(new GetSettingsQuery());

        $this->assertInstanceOf(SettingsDto::class, $dto);
        $this->assertSame('Janus', $dto->projectName);
        $this->assertSame('en-US', $dto->defaultLanguage);
        $this->assertSame('auto', $dto->defaultAppearance);
    }

    public function testHandleAlwaysCallsGetOrCreate(): void
    {
        $repository = $this->createMock(SettingsRepositoryInterface::class);
        $repository
            ->expects($this->exactly(3))
            ->method('getOrCreate')
            ->willReturn(new Settings());

        $handler = new GetSettingsHandler($repository);
        $handler->handle(new GetSettingsQuery());
        $handler->handle(new GetSettingsQuery());
        $handler->handle(new GetSettingsQuery());
    }
}
