<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;

use App\Portals\Application\Command\TriggerMagnetRunCommand;
use App\Portals\Application\DTO\MagnetRunDto;
use App\Portals\Domain\Entity\MagnetRun;
use App\Portals\Domain\Exception\MagnetNotFoundException;
use App\Portals\Domain\Message\MagnetRunMessage;
use App\Portals\Domain\Repository\MagnetRepositoryInterface;
use App\Portals\Domain\Repository\MagnetRunRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class TriggerMagnetRunHandler
{
    public function __construct(
        private readonly MagnetRepositoryInterface    $magnetRepository,
        private readonly MagnetRunRepositoryInterface $runRepository,
        private readonly MessageBusInterface          $bus,
    ) {}

    public function handle(TriggerMagnetRunCommand $command): MagnetRunDto
    {
        $magnet = $this->magnetRepository->findById($command->magnetId);
        if ($magnet === null) {
            throw new MagnetNotFoundException($command->magnetId);
        }

        $run = new MagnetRun($magnet->getId(), $command->webhookPayload);
        $this->runRepository->save($run);

        $this->bus->dispatch(new MagnetRunMessage(
            magnetId:    $magnet->getId(),
            magnetRunId: $run->getId(),
        ));

        return MagnetRunDto::fromEntity($run);
    }
}
