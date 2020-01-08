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

        // TODO : Change when the Workflow Component will be set
        $member->setStatus($this->workflowProcessor->getInitialStatus());
        $member->getStructure()->setStatus('DRAFT');

        $member->setPassword($this->encodePassword($member, $member->getPassword()));

        return $this->userRepository->save($member);
    }
}
