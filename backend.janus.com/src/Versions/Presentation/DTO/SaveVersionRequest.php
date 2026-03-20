<?php

declare(strict_types=1);

namespace App\Versions\Presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class SaveVersionRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    public string $collection = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 36)]
    public string $item = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    public string $key = 'main';

    #[Assert\NotNull]
    #[Assert\Type('array')]
    public mixed $data = null;

    public mixed $delta = null;
}
