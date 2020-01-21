<?php

namespace App\Infrastructure\Voter\Notification;

use App\Domain\Notification\DTO\SimpleNotificationCreate;
use App\Domain\User\Entity\Member;
use App\Domain\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class SimpleNotificationVoter extends Voter
{
    public const NOTIFY = 'notify';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::NOTIFY])
            && $subject instanceof SimpleNotificationCreate
        ;
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
            case self::NOTIFY:
                return $this->canNotify($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canNotify(SimpleNotificationCreate $subject, User $user)
    {
        // If the user is a member of the Post's one
        return $user instanceof Member && $user->getStructure()->getId() === $subject->getSite()->getId();
    }
}
