<?php

namespace App\Domain\User\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Post\Workflow\WorkflowProcessorInterface;
use App\Domain\Structure\Manager\StructureManagerChain;
use App\Domain\User\Entity\PasswordEncoderInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;

abstract class AbstractUserManager implements UserManagerInterface
{
    protected const ARCHIVE_ACTION = 'archive';

    private PasswordEncoderInterface $passwordEncoder;

    protected WorkflowProcessorInterface $workflowProcessor;

    protected UserRepositoryInterface $userRepository;

    protected StructureManagerChain $structureManagerChain;

    public function __construct(
        PasswordEncoderInterface $passwordEncoder,
        WorkflowProcessorInterface $workflowProcessor,
        UserRepositoryInterface $userRepository,
        StructureManagerChain $structureManagerChain
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->workflowProcessor = $workflowProcessor;
        $this->userRepository = $userRepository;
        $this->structureManagerChain = $structureManagerChain;
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

        throw new ConflictException('L\'utilisateur ne peut pas être archivé');
    }
}
