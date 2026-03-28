<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Source;

/**
 * Implemented by source adapters that receive inbound push data (webhooks).
 * The message handler injects the payload from MagnetRun before calling import().
 */
interface WebhookPayloadAwareInterface
{
    public function setPayload(array $payload): void;
}
