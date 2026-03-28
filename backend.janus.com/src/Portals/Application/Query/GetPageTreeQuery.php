<?php
declare(strict_types=1);
namespace App\Portals\Application\Query;
final class GetPageTreeQuery
{
    public function __construct(public readonly string $portalId) {}
}
