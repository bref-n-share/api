<?php

namespace App\Application\Controller\Post;

use App\Application\Controller\RestAPIController;
use App\Application\Exception\ValidationException;
use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Serializer\EntitySerializerInterface;
use App\Domain\Post\Manager\RequestManager;
use App\Domain\Post\Entity\Request as RequestPost;
use App\Domain\Post\Repository\PostRepository;
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
            $postRepository->retrieveAllBySite($id),
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

        return $this->apiJsonResponse(
            $user instanceof Donor ?
                $requestManager->retrieveAllBySites($user->getSites()->getValues()) : $requestManager->retrieveAll(),
            Response::HTTP_OK,
            $this->getLevel($request),
            $serializer
        );
    }
}
