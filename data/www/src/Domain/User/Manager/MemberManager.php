<?php

namespace App\Domain\User\Manager;

use App\Application\Exception\ConflictException;
use App\Domain\User\Entity\Member;
use App\Domain\User\Entity\PasswordEncoderInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\MemberRepositoryInterface;

class MemberManager extends AbstractUserManager
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(
        PasswordEncoderInterface $passwordEncoder,
        MemberRepositoryInterface $memberRepository
    ) {
        parent::__construct($passwordEncoder);
        $this->memberRepository = $memberRepository;
    }

    public function create(User $member): User
    {
        if (!($member instanceof Member)) {
            throw new ConflictException('Must be an instance of ' . Member::class);
        }

        // TODO : Change when the Workflow Component will be set
        $member->setStatus('DRAFT');
        $member->getStructure()->setStatus('DRAFT');

        $member->setPassword($this->encodePassword($member, $member->getPassword()));

        return $this->memberRepository->save($member);
    }
}
