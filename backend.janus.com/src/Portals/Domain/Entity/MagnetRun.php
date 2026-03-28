<?php
declare(strict_types=1);
namespace App\Portals\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'magnet_runs')]
final class MagnetRun
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(name: 'magnet_id', type: 'string', length: 36)]
    private string $magnetId;

    #[ORM\Column(name: 'started_at')]
    private \DateTimeImmutable $startedAt;

    #[ORM\Column(name: 'finished_at', nullable: true)]
    private ?\DateTimeImmutable $finishedAt = null;

    #[ORM\Column(name: 'items_imported')]
    private int $itemsImported = 0;

    #[ORM\Column(name: 'errors_json', type: 'json', nullable: true)]
    private ?array $errorsJson = null;

    #[ORM\Column(name: 'webhook_payload', type: 'json', nullable: true)]
    private ?array $webhookPayload = null;

    public function __construct(string $magnetId, ?array $webhookPayload = null)
    {
        $this->id             = Uuid::v7()->toRfc4122();
        $this->magnetId       = $magnetId;
        $this->webhookPayload = $webhookPayload;
        $this->startedAt      = new \DateTimeImmutable();
    }

    public function finish(int $itemsImported, array $errors = []): void
    {
        $this->finishedAt    = new \DateTimeImmutable();
        $this->itemsImported = $itemsImported;
        $this->errorsJson    = empty($errors) ? null : $errors;
    }

    public function getId(): string                     { return $this->id; }
    public function getMagnetId(): string               { return $this->magnetId; }
    public function getStartedAt(): \DateTimeImmutable   { return $this->startedAt; }
    public function getFinishedAt(): ?\DateTimeImmutable { return $this->finishedAt; }
    public function getItemsImported(): int              { return $this->itemsImported; }
    public function getErrors(): array                   { return $this->errorsJson ?? []; }
    public function getWebhookPayload(): ?array          { return $this->webhookPayload; }

    public function isFinished(): bool { return $this->finishedAt !== null; }
}
