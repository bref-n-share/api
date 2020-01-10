<?php

namespace App\Application\Controller\Post;

use App\Application\Controller\RestAPIController;
use App\Domain\Core\Serializer\EntitySerializerInterface;
use App\Domain\Post\Repository\CategoryRepositoryInterface;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/category")
 */
class CategoryController extends RestAPIController
{
    /**
     * @Route(name="category_get_all", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="All Categories",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type="App\Domain\Post\Entity\Category", groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Category")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param CategoryRepositoryInterface $repository
     *
     * @return Response
     */
    public function getAll(
        Request $request,
        EntitySerializerInterface $serializer,
        CategoryRepositoryInterface $repository
    ): Response {
        return $this->apiJsonResponse(
            $repository->retrieveAll(),
            Response::HTTP_OK,
            $this->getLevel($request),
            $serializer
        );
    }
}
