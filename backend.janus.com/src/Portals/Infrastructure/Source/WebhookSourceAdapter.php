<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Source;

use App\Portals\Domain\Entity\Magnet;
use App\Portals\Domain\ValueObject\SourceType;

/**
 * Processes inbound webhook payloads and maps fields into the target collection.
 *
 * source_config keys:
 *   secret     (string) — shared secret validated by the WebhookController
 *   field_map  (array)  — maps payload field names to collection field names
 *                         e.g. {"event": "type", "object.id": "ref_id"}
 *   wrap_key   (string) — optional: if the payload is an envelope, the key
 *                         that holds the array of items (e.g. "records")
 *                         Omit to treat the entire payload as one item.
 *
 * The payload is injected by MagnetRunMessageHandler before import() is called.
 */
final class WebhookSourceAdapter extends AbstractSourceAdapter implements WebhookPayloadAwareInterface
{
    private ?array $payload = null;

    public function supports(SourceType $type): bool
    {
        return $type === SourceType::WEBHOOK;
    }

    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    public function import(Magnet $magnet): int
    {
        if ($this->payload === null) {
            throw new \RuntimeException('Webhook adapter: no payload injected before import.');
        }

        $config   = $magnet->getSourceConfig();
        $fieldMap = $config->get('field_map', []);
        $wrapKey  = $config->get('wrap_key', '');

        $items = $this->resolveItems($this->payload, (string) $wrapKey);

        $count = 0;
        foreach ($items as $item) {
            $record = $this->mapItem((array) $item, (array) $fieldMap);
            if (!empty($record)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * If wrap_key is set, extract the nested array; otherwise treat payload as one item.
     */
    private function resolveItems(array $payload, string $wrapKey): array
    {
        if ($wrapKey === '') {
            // Single-item envelope — wrap in array so the loop is uniform
            return [$payload];
        }

        $items = $payload[$wrapKey] ?? null;

        if (!is_array($items)) {
            throw new \RuntimeException(sprintf(
                'Webhook adapter: wrap_key "%s" did not resolve to an array in payload.',
                $wrapKey
            ));
        }

        return $items;
    }

    private function mapItem(array $item, array $fieldMap): array
    {
        $record = [];
        foreach ($fieldMap as $sourceField => $collectionField) {
            $value = $this->extractDotPath($item, (string) $sourceField);
            if ($value !== null) {
                $record[$collectionField] = $value;
            }
        }
        return $record;
    }

    /**
     * Supports dot-notation for nested fields: e.g. "object.id" → $item['object']['id']
     */
    private function extractDotPath(array $data, string $path): mixed
    {
        $parts  = explode('.', $path);
        $cursor = $data;

        foreach ($parts as $part) {
            if (!is_array($cursor) || !array_key_exists($part, $cursor)) {
                return null;
            }
            $cursor = $cursor[$part];
        }

        return $cursor;
    }
}
