<?php

namespace App\Infrastructure\Voter\Post;

use App\Domain\Post\Entity\Post;
use App\Domain\User\Entity\Member;
use App\Domain\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PostVoter extends Voter
{
    public const CREATE = 'create';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::CREATE]) && $subject instanceof Post;
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
            case self::CREATE:
                return $this->canCreate($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canCreate(Post $subject, User $user)
    {
        return $user instanceof Member && $user->getStructure()->getId() === $subject->getSite()->getId();
    }
}
