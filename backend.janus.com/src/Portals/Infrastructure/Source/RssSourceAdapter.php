<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Source;

use App\Portals\Domain\Entity\Magnet;
use App\Portals\Domain\ValueObject\SourceType;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Fetches an RSS/Atom feed and maps entries into the target collection.
 *
 * source_config keys:
 *   url        (string) — feed URL
 *   field_map  (array)  — maps RSS field names to collection field names
 *                         e.g. {"title": "name", "link": "url", "description": "body"}
 */
final class RssSourceAdapter extends AbstractSourceAdapter
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {}

    public function supports(SourceType $type): bool
    {
        return $type === SourceType::RSS;
    }

    public function import(Magnet $magnet): int
    {
        $config   = $magnet->getSourceConfig();
        $url      = $config->get('url');
        $fieldMap = $config->get('field_map', []);

        if (empty($url)) {
            throw new \RuntimeException('RSS adapter: "url" is required in source_config.');
        }

        $response = $this->httpClient->request('GET', $url);
        $xml      = new \SimpleXMLElement($response->getContent());

        $items = $xml->channel->item ?? $xml->entry ?? [];
        $count = 0;

        foreach ($items as $item) {
            $record = $this->mapItem($item, $fieldMap);
            // Items are mapped here; actual persistence into the target collection
            // will be wired once the Items module write-path is accessible.
            // For now we count the mappable items to report progress.
            if (!empty($record)) {
                $count++;
            }
        }

        return $count;
    }

    private function mapItem(\SimpleXMLElement $item, array $fieldMap): array
    {
        $record = [];
        foreach ($fieldMap as $rssField => $collectionField) {
            $value = (string) ($item->$rssField ?? '');
            if ($value !== '') {
                $record[$collectionField] = $value;
            }
        }
        return $record;
    }
}
