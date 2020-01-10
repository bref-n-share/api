<?php

namespace App\Application\Controller\User;

use App\Application\Controller\RestAPIController;
use App\Application\Exception\ValidationException;
use App\Domain\Core\DTO\EntityId;
use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Serializer\EntitySerializerInterface;
use App\Domain\Structure\Entity\Site;
use App\Domain\User\Entity\Donor;
use App\Domain\User\Entity\Member;
use App\Domain\User\Entity\User;
use App\Domain\User\Manager\DonorManager;
use App\Domain\User\Manager\MemberManager;
use App\Domain\User\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Ramsey\Uuid\Uuid;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v1/user")
 */
class AccountController extends RestAPIController
{
    /**
     * @Route("/member", name="user_member_create", methods="POST")
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
     * @SWG\Tag(name="Member")
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
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        } catch (UniqueConstraintViolationException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage('L\'adresse email existe déjà'),
                Response::HTTP_CONFLICT
            );
        } catch (ValidationException $exception) {
            return $this->apiJsonResponse($this->formatErrorMessage($exception->getMessage()), Response::HTTP_CONFLICT);
        }

        return $this->apiJsonResponse($entity, Response::HTTP_CREATED, $this->getLevel($request), $serializer);
    }

    /**
     * @Route("/donor", name="user_donor_create", methods="POST")
     *
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Donor fields",
     *     type="json",
     *     required=true,
     *    @Model(type=Donor::class, groups={"creation"})
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Created Donor",
     *     @Model(type=Donor::class, groups={"full"})
     * )
     * @SWG\Tag(name="Donor")
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
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        } catch (UniqueConstraintViolationException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage('L\'adresse email existe déjà'),
                Response::HTTP_CONFLICT
            );
        } catch (ValidationException $exception) {
            return $this->apiJsonResponse($this->formatErrorMessage($exception->getMessage()), Response::HTTP_CONFLICT);
        }

        return $this->apiJsonResponse($entity, Response::HTTP_CREATED, $this->getLevel($request), $serializer);
    }

    /**
     * @Route("/donor/{id}", name="user_donor_get", methods="GET")
     *
     * @SWG\Parameter(
     *     description="Id of the Donor",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Requested donor",
     *     @Model(type=Donor::class, groups={"full"})
     * )
     * @SWG\Tag(name="Donor")
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
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        }

        return $this->apiJsonResponse($donor, Response::HTTP_OK, $this->getLevel($request), $serializer);
    }

    /**
     * @Route("/member/{id}", name="user_member_get", methods="GET")
     *
     * @SWG\Parameter(
     *     description="Id of the Member",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Requested Member",
     *     @Model(type=Member::class, groups={"full"})
     * )
     * @SWG\Tag(name="Member")
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
            $member = $memberManager->retrieve($id);
        } catch (NotFoundHttpException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        }

        return $this->apiJsonResponse($member, Response::HTTP_OK, $this->getLevel($request), $serializer);
    }

    /**
     * @Route("/donor/{id}", name="user_donor_archive", methods="DELETE")
     *
     * @SWG\Parameter(
     *     description="Id of the Donor",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Response(
     *     response=204,
     *     description="No content",
     * )
     * @SWG\Tag(name="Donor")
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
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        }

        return $this->apiJsonResponse('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/member/{id}", name="user_member_archive", methods="DELETE")
     *
     * @SWG\Parameter(
     *     description="Id of the Member",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Response(
     *     response=204,
     *     description="No content",
     * )
     * @SWG\Tag(name="Member")
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
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        }

        return $this->apiJsonResponse('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/donor/favorite/add", name="user_donor_favorite_add", methods="POST")
     *
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Site id",
     *     type="json",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(
     *              property="id",
     *              type="string",
     *              example="b38e4898-597a-4783-822c-c97573199124"
     *         )
     *     )
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Added site",
     *     @Model(type=Site::class, groups={"full"})
     * )
     * @SWG\Tag(name="Donor")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param ValidatorInterface $validator
     *
     * @param DonorManager $donorManager
     * @return Response
     */
    public function addFavorite(
        Request $request,
        EntitySerializerInterface $serializer,
        ValidatorInterface $validator,
        DonorManager $donorManager
    ): Response {
        try {
            $user = $this->getUser();

            if (!($user instanceof Donor)) {
                return $this->apiJsonResponse(
                    $this->formatErrorMessage('L\'utilisateur n\'est pas un donneur'),
                    Response::HTTP_UNAUTHORIZED
                );
            }

            /** @var EntityId $entityId */
            $entityId = $serializer->deserialize($request->getContent(), EntityId::class, 'json');
            $validation = $validator->validate($entityId);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            $site = $serializer->denormalize($entityId->getId(), Site::class);
            $donorManager->addFavorite($user, $site);
        } catch (NotFoundHttpException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        } catch (ValidationException $exception) {
            return $this->apiJsonResponse($this->formatErrorMessage($exception->getMessage()), Response::HTTP_CONFLICT);
        }

        return $this->apiJsonResponse($site, Response::HTTP_OK, $this->getLevel($request), $serializer);
    }

    /**
     * @Route("/donor/favorite/remove", name="user_donor_favorite_remove", methods="POST")
     *
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Site id",
     *     type="json",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(
     *              property="id",
     *              type="string",
     *              example="b38e4898-597a-4783-822c-c97573199124"
     *         )
     *     )
     * )
     * @SWG\Response(
     *     response=204,
     *     description="No content",
     * )
     * @SWG\Tag(name="Donor")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param ValidatorInterface $validator
     *
     * @param DonorManager $donorManager
     * @return Response
     */
    public function removeFavorite(
        Request $request,
        EntitySerializerInterface $serializer,
        ValidatorInterface $validator,
        DonorManager $donorManager
    ): Response {
        try {
            $user = $this->getUser();

            if (!($user instanceof Donor)) {
                return $this->apiJsonResponse(
                    $this->formatErrorMessage('L\'utilisateur n\'est pas un donneur'),
                    Response::HTTP_UNAUTHORIZED
                );
            }

            /** @var EntityId $entityId */
            $entityId = $serializer->deserialize($request->getContent(), EntityId::class, 'json');
            $validation = $validator->validate($entityId);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            $donorManager->removeFavorite($user, $serializer->denormalize($entityId->getId(), Site::class));
        } catch (NotFoundHttpException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        } catch (ValidationException $exception) {
            return $this->apiJsonResponse($this->formatErrorMessage($exception->getMessage()), Response::HTTP_CONFLICT);
        }

        return $this->apiJsonResponse('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/account", name="user_get_account", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Requested User",
     *     @Model(type=User::class, groups={"full"})
     * )
     * @SWG\Tag(name="User")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param UserRepository $repository
     *
     * @return Response
     */
    public function getMyAccount(
        Request $request,
        EntitySerializerInterface $serializer,
        UserRepository $repository
    ): Response {
        try {
            /** @var User $user */
            $user = $this->getUser();

            $entity = $repository->retrieve(Uuid::fromString($user->getId()));
        } catch (NotFoundHttpException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        }

        return $this->apiJsonResponse($entity, Response::HTTP_OK, $this->getLevel($request), $serializer);
    }
}
