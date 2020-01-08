<?php

namespace App\Application\Controller\Structure;

use App\Application\Controller\RestAPIController;
use App\Domain\Core\Serializer\EntitySerializerInterface;
use App\Domain\Structure\Manager\StructureManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

class StructureController extends RestAPIController
{
    /**
     * @Route("/api/v1/structure", name="structure_get_all", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Get all Structure",
     * )
     * @SWG\Tag(name="structures")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param StructureManager $structureManager
     *
     * @return Response
     */
    public function getAll(
        Request $request,
        EntitySerializerInterface $serializer,
        StructureManager $structureManager
    ): Response {
        return $this->apiJsonResponse(
            $structureManager->retrieveAll(),
            Response::HTTP_OK,
            $this->getLevel($request),
            $serializer
        );
    }
}
