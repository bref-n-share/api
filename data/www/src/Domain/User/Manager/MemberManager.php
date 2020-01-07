<?php

namespace App\Domain\User\Manager;

use App\Domain\User\Entity\Member;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\MemberRepositoryInterface;

class MemberManager implements UserManagerInterface
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(
        MemberRepositoryInterface $memberRepository
    ) {
        $this->memberRepository = $memberRepository;
    }

    public function create(User $memberDTO): User
    {
        $entity = $this->memberRepository->save($this->userAdapterInterface->adaptToMember($memberDTO));

        return $this->userAdapterInterface->adaptToMemberDTO($entity);
    }
}
