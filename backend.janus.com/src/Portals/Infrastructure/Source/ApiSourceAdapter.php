<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Source;

use App\Portals\Domain\Entity\Magnet;
use App\Portals\Domain\ValueObject\SourceType;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Fetches data from a REST API endpoint and maps fields into the target collection.
 *
 * source_config keys:
 *   url         (string) — endpoint URL
 *   method      (string) — HTTP method, default GET
 *   auth_header (string) — optional Authorization header value
 *   data_path   (string) — dot-notation path to the items array in the response
 *                          e.g. "data" or "results.items"
 *   field_map   (array)  — maps response field names to collection field names
 */
final class ApiSourceAdapter extends AbstractSourceAdapter
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {}

    public function supports(SourceType $type): bool
    {
        return $type === SourceType::API;
    }

    public function import(Magnet $magnet): int
    {
        $config     = $magnet->getSourceConfig();
        $url        = $config->get('url');
        $method     = strtoupper((string) $config->get('method', 'GET'));
        $authHeader = $config->get('auth_header');
        $dataPath   = $config->get('data_path', '');
        $fieldMap   = $config->get('field_map', []);

        if (empty($url)) {
            throw new \RuntimeException('API adapter: "url" is required in source_config.');
        }

        $options = [];
        if (!empty($authHeader)) {
            $options['headers']['Authorization'] = $authHeader;
        }

        $response = $this->httpClient->request($method, $url, $options);
        $body     = $response->toArray();

        $items = $this->extractPath($body, $dataPath);

        if (!is_array($items)) {
            throw new \RuntimeException(sprintf(
                'API adapter: data_path "%s" did not resolve to an array.',
                $dataPath
            ));
        }

        $count = 0;
        foreach ($items as $item) {
            $record = $this->mapItem((array) $item, $fieldMap);
            if (!empty($record)) {
                $count++;
            }
        }

        return $count;
    }

    private function extractPath(array $data, string $path): mixed
    {
        if ($path === '') {
            return $data;
        }

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

    private function mapItem(array $item, array $fieldMap): array
    {
        $record = [];
        foreach ($fieldMap as $sourceField => $collectionField) {
            if (array_key_exists($sourceField, $item)) {
                $record[$collectionField] = $item[$sourceField];
            }
        }
        return $record;
    }
}
