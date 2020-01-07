<?php

namespace App\Application\Controller\User;

use App\Application\Exception\ValidationException;
use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Serializer\EntitySerializerInterface;
use App\Domain\Structure\Manager\StructureManager;
use App\Domain\User\Entity\Donor;
use App\Domain\User\Entity\Member;
use App\Domain\User\Manager\DonorManager;
use App\Domain\User\Manager\MemberManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/user/member", name="user_member_create", methods="POST")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param StructureManager $structureManager
     * @param MemberManager $memberManager
     *
     * @return Response
     */
    public function createMember(
        Request $request,
        EntitySerializerInterface $serializer,
        ValidatorInterface $validator,
        StructureManager $structureManager,
        MemberManager $memberManager
    ): Response {
        try {
            /** @var Member $member */
            $member = $serializer->deserialize($request->getContent(), Member::class, 'json');

            if (!$member->getStructure()) {
                $requestBody = json_decode($request->getContent(), 'true');

                if (!isset($requestBody['structure_id'])) {
                    throw new ConflictException('The option \'structure_id\' must be defined');
                }

                $member->setStructure($structureManager->retrieve($requestBody['structure_id']));
            }

            $validation = $validator->validate($member);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            $entity = $memberManager->create($member);
        } catch (NotFoundHttpException | ConflictException $exception) {
            return $this->json($exception->getMessage(), $exception->getStatusCode());
        } catch (UniqueConstraintViolationException | ValidationException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_CONFLICT);
        }

        return $this->json($entity, Response::HTTP_CREATED);
    }

    /**
     * @Route("/user/donor", name="user_donor_create", methods="POST")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param DonorManager $donorManager
     *
     * @return Response
     */
    public function createDonor(
        Request $request,
        EntitySerializerInterface $serializer,
        ValidatorInterface $validator,
        DonorManager $donorManager
    ): Response {
        try {
            /** @var Donor $donor */
            $donor = $serializer->deserialize($request->getContent(), Donor::class, 'json');
            $validation = $validator->validate($donor);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            $entity = $donorManager->create($donor);
        } catch (NotFoundHttpException | ConflictException $exception) {
            return $this->json($exception->getMessage(), $exception->getStatusCode());
        } catch (UniqueConstraintViolationException | ValidationException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_CONFLICT);
        }

        return $this->json($entity, Response::HTTP_CREATED);
    }
}
