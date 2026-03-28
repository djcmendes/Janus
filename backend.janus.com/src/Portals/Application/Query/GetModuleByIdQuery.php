<?php
declare(strict_types=1);
namespace App\Portals\Application\Query;
final class GetModuleByIdQuery
{
    public function __construct(public readonly string $id) {}
}
