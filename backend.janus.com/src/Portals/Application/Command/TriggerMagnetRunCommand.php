<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;

final class TriggerMagnetRunCommand
{
    public function __construct(
        public readonly string $magnetId,
        public readonly ?array $webhookPayload = null,
    ) {}
}
