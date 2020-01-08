<?php

namespace App\Application\Controller\User;

use App\Application\Controller\RestAPIController;
use App\Application\Exception\ValidationException;
use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Serializer\EntitySerializerInterface;
use App\Domain\User\Entity\Donor;
use App\Domain\User\Entity\Member;
use App\Domain\User\Manager\DonorManager;
use App\Domain\User\Manager\MemberManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AccountController extends RestAPIController
{
    /**
     * @Route("/user/member", name="user_member_create", methods="POST")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param MemberManager $memberManager
     *
     * @return Response
     */
    public function createMember(
        Request $request,
        EntitySerializerInterface $serializer,
        ValidatorInterface $validator,
        MemberManager $memberManager
    ): Response {
        try {
            /** @var Member $member */
            $member = $serializer->deserialize($request->getContent(), Member::class, 'json');
            $validation = $validator->validate($member);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            $entity = $memberManager->create($member);
        } catch (NotFoundHttpException | ConflictException $exception) {
            return $this->apiJsonResponse($exception->getMessage(), $exception->getStatusCode());
        } catch (UniqueConstraintViolationException $exception) {
            return $this->apiJsonResponse('L\'adresse email existe déjà', Response::HTTP_CONFLICT);
        } catch (ValidationException $exception) {
            return $this->apiJsonResponse($exception->getMessage(), Response::HTTP_CONFLICT);
        }

        return $this->apiJsonResponse($entity, Response::HTTP_CREATED, $this->getLevel($request), $serializer);
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
            return $this->apiJsonResponse($exception->getMessage(), $exception->getStatusCode());
        } catch (UniqueConstraintViolationException $exception) {
            return $this->apiJsonResponse('L\'adresse email existe déjà', Response::HTTP_CONFLICT);
        } catch (ValidationException $exception) {
            return $this->apiJsonResponse($exception->getMessage(), Response::HTTP_CONFLICT);
        }

        return $this->apiJsonResponse($entity, Response::HTTP_CREATED, $this->getLevel($request), $serializer);
    }

    /**
     * @Route("/user/donor/{id}", name="user_donor_get", methods="GET")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param string $id
     * @param DonorManager $donorManager
     *
     * @return Response
     */
    public function getOneDonor(
        Request $request,
        EntitySerializerInterface $serializer,
        string $id,
        DonorManager $donorManager
    ): Response {
        try {
            $donor = $donorManager->retrieve($id);
        } catch (NotFoundHttpException $exception) {
            return $this->apiJsonResponse($exception->getMessage(), $exception->getStatusCode());
        }

        return $this->apiJsonResponse($donor, Response::HTTP_OK, $this->getLevel($request), $serializer);
    }
}
