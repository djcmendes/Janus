<?php

declare(strict_types=1);

namespace App\Revisions\Domain\Exception;

final class RevisionNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Revision "%s" not found.', $id));
    }
}
