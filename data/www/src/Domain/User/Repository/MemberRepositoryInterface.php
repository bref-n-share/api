<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\Member;

interface MemberRepositoryInterface
{
    public function save(Member $member): Member;

    public function retrieve(string $id): Member;

    public function delete(string $id): void;
}
