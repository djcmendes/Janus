<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Messenger;

use App\Portals\Domain\Message\MagnetRunMessage;
use App\Portals\Domain\Repository\MagnetRepositoryInterface;
use App\Portals\Domain\Repository\MagnetRunRepositoryInterface;
use App\Portals\Infrastructure\Source\SourceAdapterRegistry;
use App\Portals\Infrastructure\Source\WebhookPayloadAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class MagnetRunMessageHandler
{
    public function __construct(
        private readonly MagnetRepositoryInterface    $magnetRepository,
        private readonly MagnetRunRepositoryInterface $runRepository,
        private readonly SourceAdapterRegistry        $adapterRegistry,
        private readonly LoggerInterface              $logger,
    ) {}

    public function __invoke(MagnetRunMessage $message): void
    {
        $magnet = $this->magnetRepository->findById($message->magnetId);
        $run    = $this->runRepository->findById($message->magnetRunId);

        if ($magnet === null || $run === null) {
            $this->logger->warning('MagnetRunMessage: magnet or run not found', [
                'magnet_id' => $message->magnetId,
                'run_id'    => $message->magnetRunId,
            ]);
            return;
        }

        if (!$magnet->isActive()) {
            $this->logger->info('MagnetRunMessage: magnet is paused, skipping', [
                'magnet_id' => $magnet->getId(),
            ]);
            $run->finish(0, ['Magnet is paused.']);
            $this->runRepository->save($run);
            return;
        }

        $this->logger->info('MagnetRunMessage: starting import', [
            'magnet_id'   => $magnet->getId(),
            'source_type' => $magnet->getSourceType()->value,
        ]);

        $errors        = [];
        $itemsImported = 0;

        try {
            $adapter = $this->adapterRegistry->get($magnet->getSourceType());

            if ($adapter instanceof WebhookPayloadAwareInterface && $run->getWebhookPayload() !== null) {
                $adapter->setPayload($run->getWebhookPayload());
            }

            $itemsImported = $adapter->import($magnet);
        } catch (\Throwable $e) {
            $this->logger->error('MagnetRunMessage: import failed', [
                'magnet_id' => $magnet->getId(),
                'error'     => $e->getMessage(),
            ]);
            $errors[] = $e->getMessage();
        }

        $run->finish($itemsImported, $errors);
        $this->runRepository->save($run);

        $this->logger->info('MagnetRunMessage: import complete', [
            'magnet_id'      => $magnet->getId(),
            'items_imported' => $itemsImported,
            'errors'         => count($errors),
        ]);
    }
}
