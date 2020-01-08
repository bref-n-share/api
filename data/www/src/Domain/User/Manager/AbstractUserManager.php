<?php

namespace App\Domain\User\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\User\Entity\PasswordEncoderInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Workflow\UserWorkflowProcessorInterface;

abstract class AbstractUserManager implements UserManagerInterface
{
    protected const ARCHIVE_ACTION = 'archive';

    private PasswordEncoderInterface $passwordEncoder;

    protected UserWorkflowProcessorInterface $workflowProcessor;

    protected UserRepositoryInterface $userRepository;

    public function __construct(
        PasswordEncoderInterface $passwordEncoder,
        UserWorkflowProcessorInterface $workflowProcessor,
        UserRepositoryInterface $userRepository
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->workflowProcessor = $workflowProcessor;
        $this->userRepository = $userRepository;
    }

    public function encodePassword(User $user, string $password): string
    {
        return $this->passwordEncoder->encodePassword($user, $password);
    }

    public function retrieve(string $id): User
    {
        return $this->userRepository->retrieve($id);
    }

    public function archive(string $id): void
    {
        $entity = $this->retrieve($id);
        if ($this->workflowProcessor->can($entity, self::ARCHIVE_ACTION)) {
            $this->workflowProcessor->apply($entity, self::ARCHIVE_ACTION);
            $this->userRepository->save($entity);

            return;
        }

        throw new ConflictException('This user can\'t be archive at this moment');
    }
}
