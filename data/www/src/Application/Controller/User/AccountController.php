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
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AccountController extends RestAPIController
{
    /**
     * @Route("/api/v1/user/member", name="user_member_create", methods="POST")
     *
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Member fields. Some other fields can be requested for the `Structure` depend on if you want  to create an Organization or a Site",
     *     type="json",
     *     required=true,
     *    @Model(type=Member::class, groups={"creation"})
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Created Member",
     *     @Model(type=Member::class, groups={"full"})
     * )
     * @SWG\Tag(name="members")
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
     * @Route("/api/v1/user/donor", name="user_donor_create", methods="POST")
     *
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Donor fields",
     *     type="json",
     *     required=true,
     *    @Model(type=Donor::class, groups={"creation"})
     * )
     *
     * @SWG\Response(
     *     response=201,
     *     description="Created Donor",
     *     @Model(type=Donor::class, groups={"full"})
     * )
     *
     * @SWG\Tag(name="donors")
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
     * @Route("/api/v1/user/donor/{id}", name="user_donor_get", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Requested donor",
     *     @Model(type=Donor::class, groups={"full"})
     * )
     * @SWG\Parameter(
     *     description="Id of the Donor",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Tag(name="donors")
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

    /**
     * @Route("/api/v1/user/member/{id}", name="user_member_get", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Requested Member",
     *     @Model(type=Member::class, groups={"full"})
     * )
     * @SWG\Parameter(
     *     description="Id of the Member",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Tag(name="members")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param string $id
     * @param MemberManager $memberManager
     *
     * @return Response
     */
    public function getOneMember(
        Request $request,
        EntitySerializerInterface $serializer,
        string $id,
        MemberManager $memberManager
    ): Response {
        try {
            $donor = $memberManager->retrieve($id);
        } catch (NotFoundHttpException $exception) {
            return $this->apiJsonResponse($exception->getMessage(), $exception->getStatusCode());
        }

        return $this->apiJsonResponse($donor, Response::HTTP_OK, $this->getLevel($request), $serializer);
    }

    /**
     * @Route("/api/v1/user/donor/{id}", name="user_donor_archive", methods="DELETE")
     *
     * @SWG\Response(
     *     response=204,
     *     description="No content",
     * )
     * @SWG\Parameter(
     *     description="Id of the Donor",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Tag(name="donors")
     *
     * @param string $id
     * @param DonorManager $donorManager
     *
     * @return Response
     */
    public function archiveDonor(string $id, DonorManager $donorManager): Response
    {
        try {
            $donorManager->archive($id);
        } catch (NotFoundHttpException | ConflictException $exception) {
            return $this->apiJsonResponse($exception->getMessage(), $exception->getStatusCode());
        }

        return $this->apiJsonResponse('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/api/v1/user/member/{id}", name="user_member_archive", methods="DELETE")
     *
     * @SWG\Response(
     *     response=204,
     *     description="No content",
     * )
     * @SWG\Parameter(
     *     description="Id of the Member",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Tag(name="members")
     *
     * @param string $id
     * @param MemberManager $memberManager
     *
     * @return Response
     */
    public function archiveMember(string $id, MemberManager $memberManager): Response
    {
        try {
            $memberManager->archive($id);
        } catch (NotFoundHttpException | ConflictException $exception) {
            return $this->apiJsonResponse($exception->getMessage(), $exception->getStatusCode());
        }

        return $this->apiJsonResponse('', Response::HTTP_NO_CONTENT);
    }
}
