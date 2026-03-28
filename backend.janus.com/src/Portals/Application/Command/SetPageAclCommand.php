<?php
declare(strict_types=1);
namespace App\Portals\Application\Command;

/**
 * Replaces the full ACL rule set for a page.
 * Each entry in $rules must have keys: role_id, permission.
 */
final class SetPageAclCommand
{
    /**
     * @param array<int, array{role_id: string, permission: string}> $rules
     */
    public function __construct(
        public readonly string $pageId,
        public readonly array  $rules,
    ) {}
}
