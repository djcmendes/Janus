<?php

/**
 * @file CreateDeploymentRequest.php
 *
 * Presentation-layer DTO for parsing and validating deployment provider creation request bodies.
 *
 * @package App\Deployments\Presentation\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Presentation\DTO;

use App\Deployments\Domain\Enum\DeploymentProviderType;

/**
 * Parses and validates the JSON body of a POST /deployments request.
 */
final class CreateDeploymentRequest
{
    /**
     * @param string                    $name     Human-readable provider name.
     * @param string                    $type     Integration type (webhook, netlify, vercel, custom).
     * @param string                    $url      Webhook or build-hook URL.
     * @param array<string, mixed>|null $options  Extra options, or null.
     * @param bool                      $isActive Whether the provider is initially active.
     */
    public function __construct(
        public readonly string  $name,
        public readonly string  $type,
        public readonly string  $url,
        public readonly ?array  $options  = null,
        public readonly bool    $isActive = true,
    ) {}

    /**
     * Constructs a CreateDeploymentRequest from a decoded JSON array.
     *
     * @param  array<string, mixed> $data Decoded request body.
     * @return self                        Validated DTO.
     *
     * @throws \InvalidArgumentException When required fields are missing or invalid.
     */
    public static function fromArray(array $data): self
    {
        $name = trim((string) ($data['name'] ?? ''));

        if ($name === '') {
            throw new \InvalidArgumentException('name is required.');
        }

        if (strlen($name) > 255) {
            throw new \InvalidArgumentException('name must not exceed 255 characters.');
        }

        $type = (string) ($data['type'] ?? '');
        $validTypes = array_column(DeploymentProviderType::cases(), 'value');

        if (!in_array($type, $validTypes, true)) {
            throw new \InvalidArgumentException(
                sprintf('type must be one of: %s.', implode(', ', $validTypes))
            );
        }

        $url = trim((string) ($data['url'] ?? ''));

        if ($url === '') {
            throw new \InvalidArgumentException('url is required.');
        }

        $options  = isset($data['options']) && is_array($data['options']) ? $data['options'] : null;
        $isActive = isset($data['isActive']) ? (bool) $data['isActive'] : true;

        return new self($name, $type, $url, $options, $isActive);
    }
}
