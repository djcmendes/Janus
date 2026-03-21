<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\Handler\Test;

use App\Settings\Application\Command\Handler\UpdateSettingsHandler;
use App\Settings\Application\Command\UpdateSettingsCommand;
use App\Settings\Application\DTO\SettingsDto;
use App\Settings\Domain\Entity\Settings;
use App\Settings\Domain\Repository\SettingsRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class UpdateSettingsHandlerTest extends TestCase
{
    private SettingsRepositoryInterface $repository;
    private Settings $settings;
    private UpdateSettingsHandler $handler;

    protected function setUp(): void
    {
        $this->settings   = new Settings();
        $this->repository = $this->createMock(SettingsRepositoryInterface::class);
        $this->repository->method('getOrCreate')->willReturn($this->settings);
        $this->handler    = new UpdateSettingsHandler($this->repository);
    }

    public function testHandleUpdatesProjectName(): void
    {
        $command = new UpdateSettingsCommand(projectName: 'My CMS');

        $dto = $this->handler->handle($command);

        $this->assertInstanceOf(SettingsDto::class, $dto);
        $this->assertSame('My CMS', $dto->projectName);
    }

    public function testHandleUpdatesDefaultLanguage(): void
    {
        $command = new UpdateSettingsCommand(defaultLanguage: 'pt-BR');

        $dto = $this->handler->handle($command);

        $this->assertSame('pt-BR', $dto->defaultLanguage);
    }

    public function testHandleUpdatesDefaultAppearance(): void
    {
        $command = new UpdateSettingsCommand(defaultAppearance: 'dark');

        $dto = $this->handler->handle($command);

        $this->assertSame('dark', $dto->defaultAppearance);
    }

    public function testHandleUpdatesProjectUrlWhenExplicitlyProvided(): void
    {
        $command = new UpdateSettingsCommand(projectUrl: 'https://mysite.com');

        $dto = $this->handler->handle($command);

        $this->assertSame('https://mysite.com', $dto->projectUrl);
    }

    public function testHandleSetsProjectUrlToNullWhenNullProvided(): void
    {
        // First set a value
        $this->settings->setProjectUrl('https://existing.com');

        $command = new UpdateSettingsCommand(projectUrl: null);

        $dto = $this->handler->handle($command);

        $this->assertNull($dto->projectUrl);
    }

    public function testHandleDoesNotChangeProjectUrlWhenSentinelProvided(): void
    {
        $this->settings->setProjectUrl('https://keep-this.com');

        // Default value for projectUrl is UNCHANGED
        $command = new UpdateSettingsCommand(projectName: 'Changed Name');

        $dto = $this->handler->handle($command);

        // projectUrl should still be the original value
        $this->assertSame('https://keep-this.com', $dto->projectUrl);
        $this->assertSame('Changed Name', $dto->projectName);
    }

    public function testHandlePersistsSettings(): void
    {
        $this->repository->expects($this->once())->method('save');

        $this->handler->handle(new UpdateSettingsCommand(projectName: 'Test'));
    }

    public function testHandleWithNoChangesReturnsCurrentSettings(): void
    {
        // All defaults (nothing actually changes)
        $command = new UpdateSettingsCommand();

        $dto = $this->handler->handle($command);

        $this->assertSame('Janus', $dto->projectName);
        $this->assertSame('en-US', $dto->defaultLanguage);
    }
}
