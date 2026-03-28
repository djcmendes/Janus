<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;

use App\Portals\Application\Command\SetPageAclCommand;
use App\Portals\Application\DTO\AclRuleDto;
use App\Portals\Domain\Entity\AclRule;
use App\Portals\Domain\Exception\PageNotFoundException;
use App\Portals\Domain\Repository\AclRepositoryInterface;
use App\Portals\Domain\Repository\PageRepositoryInterface;

final class SetPageAclHandler
{
    private const SUBJECT_TYPE = 'page';

    public function __construct(
        private readonly PageRepositoryInterface $pageRepository,
        private readonly AclRepositoryInterface  $aclRepository,
    ) {}

    /** @return AclRuleDto[] */
    public function handle(SetPageAclCommand $command): array
    {
        $page = $this->pageRepository->findById($command->pageId);
        if ($page === null) {
            throw new PageNotFoundException($command->pageId);
        }

        $this->aclRepository->deleteBySubject(self::SUBJECT_TYPE, $command->pageId);

        $dtos = [];
        foreach ($command->rules as $entry) {
            $rule = new AclRule(
                subjectType: self::SUBJECT_TYPE,
                subjectId:   $command->pageId,
                roleId:      $entry['role_id'],
                permission:  $entry['permission'],
            );
            $this->aclRepository->save($rule, false);
            $dtos[] = AclRuleDto::fromEntity($rule);
        }

        $this->aclRepository->flush();

        return $dtos;
    }
}
