<?php

declare(strict_types=1);

namespace App\Schema\Presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class ApplySchemaRequest
{
    #[Assert\NotNull]
    #[Assert\Type('array')]
    public mixed $snapshot = null;

    public bool $force = false;
}
