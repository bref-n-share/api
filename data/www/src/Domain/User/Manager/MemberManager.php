<?php

namespace App\Domain\User\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\User\Entity\Member;
use App\Domain\User\Entity\User;

class MemberManager extends AbstractUserManager
{
    public function create(User $member): User
    {
        if (!($member instanceof Member)) {
            throw new ConflictException('Must be an instance of ' . Member::class);
        }

        $member->setStatus($this->workflowProcessor->getInitialStatus());
        $member->getStructure()->setStatus(
            $this->structureManagerChain->getManager($member->getStructure())->getInitialStatus()
        );

        $member->setPassword($this->encodePassword($member, $member->getPassword()));

        return $this->userRepository->save($member);
    }

    public function supports(User $user): bool
    {
        return $user instanceof Member;
    }
}
