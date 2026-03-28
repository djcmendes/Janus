<?php

declare(strict_types=1);

namespace App\Portals\Infrastructure\Security\tests;

use App\Portals\Domain\Entity\AclRule;
use App\Portals\Domain\Repository\AclRepositoryInterface;
use App\Portals\Infrastructure\Security\AclVoter;
use App\Roles\Domain\Entity\Role;
use App\Users\Domain\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Uid\Uuid;

final class AclVoterTest extends TestCase
{
    private AclRepositoryInterface&MockObject $aclRepository;
    private AclVoter $voter;

    protected function setUp(): void
    {
        $this->aclRepository = $this->createMock(AclRepositoryInterface::class);
        $this->voter         = new AclVoter($this->aclRepository);
    }

    // ── Unsupported attribute / subject ────────────────────────────────────

    public function testAbstainsOnUnsupportedAttribute(): void
    {
        $token = $this->tokenForUser($this->makeUser());
        $result = $this->voter->vote($token, 'some-id', ['UNKNOWN_ATTR']);
        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testAbstainsWhenSubjectIsNotString(): void
    {
        $token  = $this->tokenForUser($this->makeUser());
        $result = $this->voter->vote($token, ['not', 'a', 'string'], [AclVoter::PAGE_VIEW]);
        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    // ── Admin bypass ───────────────────────────────────────────────────────

    public function testAdminAlwaysGranted(): void
    {
        $user  = $this->makeUser(['ROLE_ADMIN']);
        $token = $this->tokenForUser($user);

        $this->aclRepository->expects($this->never())->method('findBySubject');

        $result = $this->voter->vote($token, 'page-uuid', [AclVoter::PAGE_VIEW]);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    // ── No ACL rules → open by default ────────────────────────────────────

    public function testGrantsAccessWhenNoRulesDefinedForPage(): void
    {
        $user  = $this->makeUser();
        $token = $this->tokenForUser($user);

        $this->aclRepository->method('findBySubject')->willReturn([]);

        $result = $this->voter->vote($token, 'page-uuid', [AclVoter::PAGE_VIEW]);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testGrantsAccessWhenNoRulesDefinedForPortal(): void
    {
        $user  = $this->makeUser();
        $token = $this->tokenForUser($user);

        $this->aclRepository->method('findBySubject')->willReturn([]);

        $result = $this->voter->vote($token, 'portal-uuid', [AclVoter::PORTAL_VIEW]);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    // ── Rules exist, matching role ─────────────────────────────────────────

    public function testGrantsAccessWhenUserRoleMatchesRule(): void
    {
        $roleId = 'role-uuid-0000-0000-0000-000000000001';
        $user   = $this->makeUserWithRole($roleId);
        $token  = $this->tokenForUser($user);

        $rule = new AclRule('page', 'page-uuid', $roleId, 'view');
        $this->aclRepository->method('findBySubject')->willReturn([$rule]);

        $result = $this->voter->vote($token, 'page-uuid', [AclVoter::PAGE_VIEW]);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testGrantsEditWhenUserRoleMatchesEditRule(): void
    {
        $roleId = 'role-uuid-0000-0000-0000-000000000001';
        $user   = $this->makeUserWithRole($roleId);
        $token  = $this->tokenForUser($user);

        $rule = new AclRule('page', 'page-uuid', $roleId, 'edit');
        $this->aclRepository->method('findBySubject')->willReturn([$rule]);

        $result = $this->voter->vote($token, 'page-uuid', [AclVoter::PAGE_EDIT]);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    // ── Rules exist, no matching role ─────────────────────────────────────

    public function testDeniesAccessWhenUserRoleDoesNotMatchAnyRule(): void
    {
        $user  = $this->makeUserWithRole('role-uuid-0000-0000-0000-000000000099');
        $token = $this->tokenForUser($user);

        $rule = new AclRule('page', 'page-uuid', 'role-uuid-0000-0000-0000-000000000001', 'view');
        $this->aclRepository->method('findBySubject')->willReturn([$rule]);

        $result = $this->voter->vote($token, 'page-uuid', [AclVoter::PAGE_VIEW]);
        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testDeniesAccessWhenRoleMatchesButPermissionDiffers(): void
    {
        $roleId = 'role-uuid-0000-0000-0000-000000000001';
        $user   = $this->makeUserWithRole($roleId);
        $token  = $this->tokenForUser($user);

        // User has 'view' rule, but asking for 'edit'
        $rule = new AclRule('page', 'page-uuid', $roleId, 'view');
        $this->aclRepository->method('findBySubject')->willReturn([$rule]);

        $result = $this->voter->vote($token, 'page-uuid', [AclVoter::PAGE_EDIT]);
        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testDeniesAccessWhenUserHasNoRole(): void
    {
        $user  = $this->makeUser(); // no role assigned
        $token = $this->tokenForUser($user);

        $rule = new AclRule('page', 'page-uuid', 'some-role-id', 'view');
        $this->aclRepository->method('findBySubject')->willReturn([$rule]);

        $result = $this->voter->vote($token, 'page-uuid', [AclVoter::PAGE_VIEW]);
        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    // ── Portal attributes ─────────────────────────────────────────────────

    public function testPortalViewUsesPortalSubjectType(): void
    {
        $roleId = 'role-uuid-0000-0000-0000-000000000001';
        $user   = $this->makeUserWithRole($roleId);
        $token  = $this->tokenForUser($user);

        $this->aclRepository
            ->expects($this->once())
            ->method('findBySubject')
            ->with('portal', 'portal-uuid')
            ->willReturn([new AclRule('portal', 'portal-uuid', $roleId, 'view')]);

        $result = $this->voter->vote($token, 'portal-uuid', [AclVoter::PORTAL_VIEW]);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function makeUser(array $roles = ['ROLE_USER']): User
    {
        $user = new User('test@example.com');
        $user->setRoles($roles);
        return $user;
    }

    private function makeUserWithRole(string $roleId): User
    {
        $user = $this->makeUser();
        $role = $this->createMock(Role::class);
        $role->method('getId')->willReturn(Uuid::fromString($roleId));
        $user->setRole($role);
        return $user;
    }

    private function tokenForUser(User $user): TokenInterface
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        return $token;
    }
}
