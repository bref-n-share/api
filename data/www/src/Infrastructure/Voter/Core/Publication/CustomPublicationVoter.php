<?php

namespace App\Infrastructure\Voter\Core\Publication;

use App\Domain\Core\DTO\CustomSocialNetworkPublicationDto;
use App\Domain\User\Entity\Member;
use App\Domain\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CustomPublicationVoter extends Voter
{
    public const PUBLISH = 'publish';

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::PUBLISH]) && $subject instanceof CustomSocialNetworkPublicationDto;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        // the user must be logged in; if not, deny access
        if (!($user instanceof User) || $user->isArchived()) {
            return false;
        }
        switch ($attribute) {
            case self::PUBLISH:
                return $this->canPublish($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canPublish($subject, User $user)
    {
        return $user instanceof Member;
    }
}
