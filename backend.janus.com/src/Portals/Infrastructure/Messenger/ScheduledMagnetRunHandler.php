<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Messenger;

use App\Portals\Application\Command\Handler\TriggerMagnetRunHandler;
use App\Portals\Application\Command\TriggerMagnetRunCommand;
use App\Portals\Domain\Message\ScheduledMagnetRunMessage;
use App\Portals\Domain\Repository\MagnetRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ScheduledMagnetRunHandler
{
    public function __construct(
        private readonly MagnetRepositoryInterface $magnetRepository,
        private readonly TriggerMagnetRunHandler   $triggerHandler,
        private readonly LoggerInterface           $logger,
    ) {}

    public function __invoke(ScheduledMagnetRunMessage $message): void
    {
        $magnet = $this->magnetRepository->findById($message->magnetId);

        if ($magnet === null) {
            $this->logger->warning('ScheduledMagnetRun: magnet not found', [
                'magnet_id' => $message->magnetId,
            ]);
            return;
        }

        if (!$magnet->isActive()) {
            $this->logger->info('ScheduledMagnetRun: magnet is not active, skipping', [
                'magnet_id' => $message->magnetId,
            ]);
            return;
        }

        $this->logger->info('ScheduledMagnetRun: triggering import', [
            'magnet_id' => $magnet->getId(),
            'name'      => $magnet->getName(),
            'schedule'  => $magnet->getSchedule(),
        ]);

        $this->triggerHandler->handle(new TriggerMagnetRunCommand($magnet->getId()));
    }
}
