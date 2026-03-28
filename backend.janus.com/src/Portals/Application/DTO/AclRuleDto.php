<?php
declare(strict_types=1);
namespace App\Portals\Application\DTO;

use App\Portals\Domain\Entity\AclRule;

final class AclRuleDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $subjectType,
        public readonly string $subjectId,
        public readonly string $roleId,
        public readonly string $permission,
    ) {}

    public static function fromEntity(AclRule $rule): self
    {
        return new self(
            id:          $rule->getId(),
            subjectType: $rule->getSubjectType(),
            subjectId:   $rule->getSubjectId(),
            roleId:      $rule->getRoleId(),
            permission:  $rule->getPermission(),
        );
    }

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'subject_type' => $this->subjectType,
            'subject_id'   => $this->subjectId,
            'role_id'      => $this->roleId,
            'permission'   => $this->permission,
        ];
    }
}
