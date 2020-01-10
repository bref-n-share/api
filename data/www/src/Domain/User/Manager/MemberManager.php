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
        $member->setStructure(
            $this->structureManagerChain->getManager($member->getStructure())
                ->getFormattedStructureFromMemberCreation($member->getStructure())
        );


        $member->addRole('ROLE_USER');

        // If the structure is created by the user, the latter is the administrator of this one
        if (!$member->getStructure()->getId()) {
            $member->addRole('ROLE_ADMIN');
        }

        $member->setPassword($this->encodePassword($member, $member->getPassword()));

        return $this->userRepository->save($member);
    }

    public function supports(User $user): bool
    {
        return $user instanceof Member;
    }
}
