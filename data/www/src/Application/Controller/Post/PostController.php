<?php

namespace App\Application\Controller\Post;

use App\Application\Controller\RestAPIController;
use App\Application\Exception\ValidationException;
use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Serializer\EntitySerializerInterface;
use App\Domain\Post\Manager\RequestManager;
use App\Domain\Post\Entity\Request as RequestPost;
use App\Domain\Post\Repository\PostRepository;
use App\Domain\Post\Repository\PostRepositoryInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostController extends RestAPIController
{
    /**
     * @Route("/api/v1/post/request", name="post_request_create", methods="POST")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Create a Request",
     * )
     *
     * @SWG\Tag(name="requests")
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

            $entity = $requestManager->create($requestPost);
        } catch (NotFoundHttpException | ConflictException $exception) {
            return $this->apiJsonResponse($exception->getMessage(), $exception->getStatusCode());
        } catch (ValidationException $exception) {
            return $this->apiJsonResponse($exception->getMessage(), Response::HTTP_CONFLICT);
        }

        return $this->apiJsonResponse($entity, Response::HTTP_CREATED, $this->getLevel($request), $serializer);
    }

    /**
     * @Route("/api/v1/post", name="post_get_all", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Get all post",
     * )
     * @SWG\Tag(name="posts")
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
}
