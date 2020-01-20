<?php

namespace App\Infrastructure\Voter\Structure;

use App\Domain\Structure\Entity\Site;
use App\Domain\User\Entity\Member;
use App\Domain\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class SiteVoter extends Voter
{
    public const UPDATE = 'update';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::UPDATE]) && $subject instanceof Site;
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
                return $this->canUpdate($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canUpdate(Site $subject, User $user)
    {
        return
            $user instanceof Member
            && $user->getStructure()->getId() === $subject->getId()
            && $this->security->isGranted('ROLE_ADMIN')
        ;
    }
}
