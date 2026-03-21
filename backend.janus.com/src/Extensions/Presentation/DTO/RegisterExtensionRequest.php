<?php

declare(strict_types=1);

namespace App\Extensions\Presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class RegisterExtensionRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name = '';

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['interface', 'endpoint', 'hook', 'operation', 'display', 'layout', 'module', 'panel'])]
    public string $type = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    public string $version = '';

    public bool    $enabled     = false;
    public ?string $description = null;
    public ?array  $meta        = null;
}
