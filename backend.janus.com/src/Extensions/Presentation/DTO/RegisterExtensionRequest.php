<?php

/**
 * @file RegisterExtensionRequest.php
 *
 * Presentation-layer DTO for deserialising and validating the POST /extensions body.
 *
 * @package App\Extensions\Presentation\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Carries validated input for registering a new extension.
 */
final class RegisterExtensionRequest
{
    /** @var string Package/bundle name. */
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name = '';

    /** @var string ExtensionType enum value string. */
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['interface', 'endpoint', 'hook', 'operation', 'display', 'layout', 'module', 'panel'])]
    public string $type = '';

    /** @var string Semantic version string. */
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    public string $version = '';

    /** @var bool Whether to enable on registration (default false). */
    public bool    $enabled     = false;

    /** @var string|null Optional human-readable description. */
    public ?string $description = null;

    /** @var array<string, mixed>|null Entry-point configuration, or null. */
    public ?array  $meta        = null;
}
