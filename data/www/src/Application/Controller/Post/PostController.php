<?php

namespace App\Application\Controller\Post;

use App\Application\Controller\RestAPIController;
use App\Application\Exception\ValidationException;
use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Serializer\EntitySerializerInterface;
use App\Domain\Post\DTO\PostEdit;
use App\Domain\Post\DTO\RequestEdit;
use App\Domain\Post\Entity\Information;
use App\Domain\Post\Entity\Post;
use App\Domain\Post\Manager\InformationManager;
use App\Domain\Post\Manager\RequestManager;
use App\Domain\Post\Entity\Request as RequestPost;
use App\Domain\Post\Repository\PostRepository;
use App\Domain\Structure\Entity\Site;
use App\Domain\User\Entity\Donor;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v1/post")
 */
class PostController extends RestAPIController
{
    /**
     * @Route("/request", name="post_request_create", methods="POST")
     *
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Request fields",
     *     type="json",
     *     required=true,
     *    @Model(type="App\Domain\Post\Entity\Request", groups={"creation"})
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Created Request",
     *     @Model(type="App\Domain\Post\Entity\Request", groups={"full"})
     * )
     * @SWG\Tag(name="Request")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param RequestManager $requestManager
     *
     * @return Response
     */
    public function createRequest(
        Request $request,
        EntitySerializerInterface $serializer,
        ValidatorInterface $validator,
        RequestManager $requestManager
    ): Response {
        try {
            /** @var RequestPost $requestPost */
            $requestPost = $serializer->deserialize($request->getContent(), RequestPost::class, 'json');
            $validation = $validator->validate($requestPost);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            $this->denyAccessUnlessGranted('create', $requestPost);

            $entity = $requestManager->create($requestPost);
        } catch (NotFoundHttpException | ConflictException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        } catch (ValidationException $exception) {
            return $this->apiJsonResponse($this->formatErrorMessage($exception->getMessage()), Response::HTTP_CONFLICT);
        }

        return $this->apiJsonResponse($entity, Response::HTTP_CREATED, $this->getLevel($request), $serializer);
    }

    /**
     * @Route(name="post_get_all", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="All Posts",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type="App\Domain\Post\Entity\Post", groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Post")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param PostRepository $postRepository
     *
     * @return Response
     */
    public function getAll(
        Request $request,
        EntitySerializerInterface $serializer,
        PostRepository $postRepository
    ): Response {
        return $this->apiJsonResponse(
            $postRepository->retrieveAll(),
            Response::HTTP_OK,
            $this->getLevel($request),
            $serializer
        );
    }

    /**
     * @Route("/site/{id}", name="post_get_all_by_site", methods="GET")
     *
     * @SWG\Parameter(
     *     description="Id of the Site",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="All Post related to the Site",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type="App\Domain\Post\Entity\Post", groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Post")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param string $id
     * @param PostRepository $postRepository
     *
     * @return Response
     */
    public function getAllBySite(
        Request $request,
        EntitySerializerInterface $serializer,
        string $id,
        PostRepository $postRepository
    ): Response {
        return $this->apiJsonResponse(
            $postRepository->retrieveBy(['site' => $id]),
            Response::HTTP_OK,
            $this->getLevel($request),
            $serializer
        );
    }

    /**
     * @Route("/request", name="post_request_get_all", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="All Requests",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type="App\Domain\Post\Entity\Request", groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Request")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param RequestManager $requestManager
     *
     * @return Response
     */
    public function getAllRequest(
        Request $request,
        EntitySerializerInterface $serializer,
        RequestManager $requestManager
    ): Response {
        $user = $this->getUser();

        $options = $this->formatQueryParameters($request->query->all());

        if ($user instanceof Donor) {
            $sites = $user->getSites()->getValues();
            $siteIds = [];
            /** @var Site $site */
            foreach ($sites as $site) {
                $siteIds[] = $site->getId()->toString();
            }

            $options['site'] = $siteIds;
        }

        return $this->apiJsonResponse(
            $requestManager->retrieveBy($options),
            Response::HTTP_OK,
            $this->getLevel($request),
            $serializer
        );
    }

    /**
     * @Route("/request/{id}", name="post_request_update", methods="PATCH")
     *
     * @SWG\Parameter(
     *     description="Id of the Request",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Request fields",
     *     type="json",
     *     required=true,
     *    @Model(type=RequestPost::class, groups={"updatable"})
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Updated Request",
     *     @Model(type=RequestPost::class, groups={"full"})
     * )
     * @SWG\Tag(name="Request")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param RequestManager $requestManager
     * @param string $id
     *
     * @return Response
     */
    public function updateRequest(
        Request $request,
        EntitySerializerInterface $serializer,
        ValidatorInterface $validator,
        RequestManager $requestManager,
        string $id
    ): Response {
        try {
            /** @var RequestEdit $requestDto */
            $requestDto = $serializer->deserialize($request->getContent(), RequestEdit::class, 'json');
            $validation = $validator->validate($requestDto);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            /** @var RequestPost $entityToSave */
            $entityToSave = $requestManager->retrieve($id);

            $this->denyAccessUnlessGranted('update', $entityToSave);

            /** @var RequestPost $requestPost */
            $requestPost = $requestManager->getUpdatedEntity($requestDto, $entityToSave);
            $validation = $validator->validate($requestPost);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            $savedEntity = $requestManager->save($requestPost);
        } catch (NotFoundHttpException | ConflictException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        } catch (ValidationException $exception) {
            return $this->apiJsonResponse($this->formatErrorMessage($exception->getMessage()), Response::HTTP_CONFLICT);
        }

        return $this->apiJsonResponse($savedEntity, Response::HTTP_OK, $this->getLevel($request), $serializer);
    }

    /**
     * @Route("/information", name="post_information_create", methods="POST")
     *
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Information fields",
     *     type="json",
     *     required=true,
     *    @Model(type="App\Domain\Post\Entity\Information", groups={"creation"})
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Created Information",
     *     @Model(type="App\Domain\Post\Entity\Information", groups={"full"})
     * )
     * @SWG\Tag(name="Information")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param InformationManager $informationManager
     *
     * @return Response
     */
    public function createInformation(
        Request $request,
        EntitySerializerInterface $serializer,
        ValidatorInterface $validator,
        InformationManager $informationManager
    ): Response {
        try {
            /** @var RequestPost $requestPost */
            $requestPost = $serializer->deserialize($request->getContent(), Information::class, 'json');
            $validation = $validator->validate($requestPost);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            $this->denyAccessUnlessGranted('create', $requestPost);

            $entity = $informationManager->create($requestPost);
        } catch (NotFoundHttpException | ConflictException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        } catch (ValidationException $exception) {
            return $this->apiJsonResponse($this->formatErrorMessage($exception->getMessage()), Response::HTTP_CONFLICT);
        }

        return $this->apiJsonResponse($entity, Response::HTTP_CREATED, $this->getLevel($request), $serializer);
    }

    /**
     * @Route("/information", name="post_information_get_all", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="All Informations",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type="App\Domain\Post\Entity\Information", groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Information")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param InformationManager $informationManager
     *
     * @return Response
     */
    public function getAllInformation(
        Request $request,
        EntitySerializerInterface $serializer,
        InformationManager $informationManager
    ): Response {
        $user = $this->getUser();

        return $this->apiJsonResponse(
            $user instanceof Donor ?
                $informationManager->retrieveAllBySites($user->getSites()->getValues()) : $informationManager->retrieveAll(),
            Response::HTTP_OK,
            $this->getLevel($request),
            $serializer
        );
    }

    /**
     * @Route("/information/{id}", name="post_information_update", methods="PATCH")
     *
     * @SWG\Parameter(
     *     description="Id of the Information",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Information fields",
     *     type="json",
     *     required=true,
     *    @Model(type=Information::class, groups={"updatable"})
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Updated Information",
     *     @Model(type=Information::class, groups={"full"})
     * )
     * @SWG\Tag(name="Information")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param InformationManager $informationManager
     * @param string $id
     *
     * @return Response
     */
    public function updateInformation(
        Request $request,
        EntitySerializerInterface $serializer,
        ValidatorInterface $validator,
        InformationManager $informationManager,
        string $id
    ): Response {
        try {
            /** @var PostEdit $postDto */
            $postDto = $serializer->deserialize($request->getContent(), PostEdit::class, 'json');
            $validation = $validator->validate($postDto);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            /** @var Information $entityToSave */
            $entityToSave = $informationManager->retrieve($id);

            $this->denyAccessUnlessGranted('update', $entityToSave);

            /** @var Information $information */
            $information = $informationManager->getUpdatedEntity($postDto, $entityToSave);
            $validation = $validator->validate($information);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            $savedEntity = $informationManager->save($information);
        } catch (NotFoundHttpException | ConflictException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        } catch (ValidationException $exception) {
            return $this->apiJsonResponse($this->formatErrorMessage($exception->getMessage()), Response::HTTP_CONFLICT);
        }

        return $this->apiJsonResponse($savedEntity, Response::HTTP_OK, $this->getLevel($request), $serializer);
    }

    /**
     * @Route("/request/{id}", name="post_request_get", methods="GET")
     *
     * @SWG\Parameter(
     *     description="Id of the Request",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Requested Request",
     *     @Model(type=RequestPost::class, groups={"full"})
     * )
     * @SWG\Tag(name="Request")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param string $id
     * @param RequestManager $requestManager
     *
     * @return Response
     */
    public function getOneRequest(
        Request $request,
        EntitySerializerInterface $serializer,
        string $id,
        RequestManager $requestManager
    ): Response {
        try {
            $requestPost = $requestManager->retrieve($id);
        } catch (NotFoundHttpException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        }

        return $this->apiJsonResponse($requestPost, Response::HTTP_OK, $this->getLevel($request), $serializer);
    }

    /**
     * @Route("/information/{id}", name="post_information_get", methods="GET")
     *
     * @SWG\Parameter(
     *     description="Id of the Information",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Requested Information",
     *     @Model(type=Information::class, groups={"full"})
     * )
     * @SWG\Tag(name="Information")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param string $id
     * @param InformationManager $informationManager
     *
     * @return Response
     */
    public function getOneInformation(
        Request $request,
        EntitySerializerInterface $serializer,
        string $id,
        InformationManager $informationManager
    ): Response {
        try {
            $information = $informationManager->retrieve($id);
        } catch (NotFoundHttpException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        }

        return $this->apiJsonResponse($information, Response::HTTP_OK, $this->getLevel($request), $serializer);
    }

    /**
     * @Route("/{id}", name="post_post_get", methods="GET")
     *
     * @SWG\Parameter(
     *     description="Id of the Post",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Requested Post",
     *     @Model(type=Post::class, groups={"full"})
     * )
     * @SWG\Tag(name="Post")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param string $id
     * @param PostRepository $postRepository
     *
     * @return Response
     */
    public function getOnePost(
        Request $request,
        EntitySerializerInterface $serializer,
        string $id,
        PostRepository $postRepository
    ): Response {
        try {
            $post = $postRepository->retrieve($id);
        } catch (NotFoundHttpException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        }

        return $this->apiJsonResponse($post, Response::HTTP_OK, $this->getLevel($request), $serializer);
    }

    /**
     * @Route("/request/{id}", name="post_request_participate", methods="POST")
     *
     * @SWG\Parameter(
     *     description="Id of the Request",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Participated Request",
     *     @Model(type=RequestPost::class, groups={"full"})
     * )
     * @SWG\Tag(name="Request")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param string $id
     * @param RequestManager $requestManager
     *
     * @return Response
     */
    public function participate(
        Request $request,
        EntitySerializerInterface $serializer,
        string $id,
        RequestManager $requestManager
    ): Response {
        try {
            $requestPost = $requestManager->participate($id);
        } catch (NotFoundHttpException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        }

        return $this->apiJsonResponse($requestPost, Response::HTTP_OK, $this->getLevel($request), $serializer);
    }
}
