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
    public const UPDATE = 'update';
    public const PUBLISH = 'publish';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::CREATE, self::UPDATE, self::PUBLISH]) && $subject instanceof Post;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        // the user must be logged in; if not, deny access
        if (!($user instanceof User) || $user->isArchived()) {
            return false;
        }

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            // Today the code is the same in these 3 methods,
            // however the right to do some action can evolve independently
            // so we prefer to separate the code.
            case self::CREATE:
                return $this->canCreate($subject, $user);
            case self::UPDATE:
                return $this->canUpdate($subject, $user);
            case self::PUBLISH:
                return $this->canPublish($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canCreate(Post $subject, User $user)
    {
        // If the user is a member of the Post's one
        return $user instanceof Member && $user->getStructure()->getId() === $subject->getSite()->getId();
    }

    private function canUpdate(Post $subject, User $user)
    {
        // If the user is a member of the Post's one
        return $user instanceof Member && $user->getStructure()->getId() === $subject->getSite()->getId();
    }

    private function canPublish(Post $subject, User $user)
    {
        // If the user is a member of the Post's one
        return $user instanceof Member && $user->getStructure()->getId() === $subject->getSite()->getId();
    }
}
