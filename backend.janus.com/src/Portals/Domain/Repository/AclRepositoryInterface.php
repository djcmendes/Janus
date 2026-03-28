<?php
declare(strict_types=1);
namespace App\Portals\Domain\Repository;

use App\Portals\Domain\Entity\AclRule;

interface AclRepositoryInterface
{
    public function save(AclRule $rule, bool $flush = true): void;
    public function delete(AclRule $rule): void;
    public function findById(string $id): ?AclRule;
    /** @return AclRule[] */
    public function findBySubject(string $subjectType, string $subjectId): array;
    /** @return AclRule[] */
    public function findByRole(string $roleId): array;
    public function deleteBySubject(string $subjectType, string $subjectId): void;
}
