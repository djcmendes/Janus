<?php

declare(strict_types=1);

namespace App\Deployments\Presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateDeploymentRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name = '';

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['webhook', 'netlify', 'vercel', 'custom'])]
    public string $type = 'webhook';

    #[Assert\NotBlank]
    #[Assert\Url]
    public string $url = '';

    public ?array $options = null;

    public bool $isActive = true;
}
