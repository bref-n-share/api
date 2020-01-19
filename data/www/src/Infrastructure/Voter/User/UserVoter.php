<?php

namespace App\Infrastructure\Voter\User;

use App\Domain\User\Entity\Member;
use App\Domain\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    public const UPDATE = 'update';
    public const ARCHIVE = 'archive';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::UPDATE, self::ARCHIVE]) && $subject instanceof User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        // the user must be logged in; if not, deny access
        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case self::UPDATE:
            case self::ARCHIVE:
                return $this->canUpdate($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canUpdate(User $userToUpdate, User $user)
    {
        if ($userToUpdate->getId() === $user->getId()) {
            return true;
        }

        if ($userToUpdate instanceof Member && $user instanceof Member) {
            return $this->canUpdateMember($userToUpdate, $user);
        }

        return false;
    }

    private function canUpdateMember(Member $userToUpdate, Member $user)
    {
        return $userToUpdate->getStructure()->getId() === $user->getStructure()->getId()
            && $this->security->isGranted('ROLE_ADMIN');
    }
}
