<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Security;

use App\Portals\Domain\Repository\AclRepositoryInterface;
use App\Users\Domain\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Checks ACL rules stored in the acl_rules table for portal/page access.
 *
 * Supported attributes: PAGE_VIEW, PAGE_EDIT, PORTAL_VIEW, PORTAL_EDIT
 * Subject: the entity ID (string)
 *
 * Usage in a controller:
 *   $this->denyAccessUnlessGranted('PAGE_VIEW', $pageId);
 *   $this->denyAccessUnlessGranted('PORTAL_EDIT', $portalId);
 *
 * If no ACL rules are defined for the subject, access is granted by default
 * (open by default; explicit deny is handled by defining rules without a matching role).
 * ROLE_ADMIN always bypasses ACL checks.
 */
final class AclVoter extends Voter
{
    public const PAGE_VIEW    = 'PAGE_VIEW';
    public const PAGE_EDIT    = 'PAGE_EDIT';
    public const PORTAL_VIEW  = 'PORTAL_VIEW';
    public const PORTAL_EDIT  = 'PORTAL_EDIT';

    private const ATTRIBUTE_SUBJECT_MAP = [
        self::PAGE_VIEW   => 'page',
        self::PAGE_EDIT   => 'page',
        self::PORTAL_VIEW => 'portal',
        self::PORTAL_EDIT => 'portal',
    ];

    private const ATTRIBUTE_PERMISSION_MAP = [
        self::PAGE_VIEW   => 'view',
        self::PAGE_EDIT   => 'edit',
        self::PORTAL_VIEW => 'view',
        self::PORTAL_EDIT => 'edit',
    ];

    public function __construct(
        private readonly AclRepositoryInterface $aclRepository,
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return isset(self::ATTRIBUTE_SUBJECT_MAP[$attribute]) && is_string($subject);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // Admins bypass ACL checks entirely
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        $subjectType = self::ATTRIBUTE_SUBJECT_MAP[$attribute];
        $permission  = self::ATTRIBUTE_PERMISSION_MAP[$attribute];
        $rules       = $this->aclRepository->findBySubject($subjectType, $subject);

        // No rules defined → open by default
        if (empty($rules)) {
            return true;
        }

        $userRoleId = $user->getRole()?->getId();
        if ($userRoleId === null) {
            return false;
        }

        foreach ($rules as $rule) {
            if ($rule->getRoleId() === (string) $userRoleId && $rule->getPermission() === $permission) {
                return true;
            }
        }

        return false;
    }
}
