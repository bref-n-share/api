<?php

namespace App\Application\Controller\Structure;

use App\Application\Controller\RestAPIController;
use App\Application\Exception\ValidationException;
use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Serializer\EntitySerializerInterface;
use App\Domain\Structure\DTO\SiteEdit;
use App\Domain\Structure\Entity\Site;
use App\Domain\Structure\Manager\SiteManager;
use App\Domain\Structure\Repository\StructureRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v1/structure")
 */
class StructureController extends RestAPIController
{
    /**
     * @Route(name="structure_get_all", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="All Structures",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type="App\Domain\Structure\Entity\Structure", groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Structure")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param StructureRepository $structureRepository
     *
     * @return Response
     */
    public function getAll(
        Request $request,
        EntitySerializerInterface $serializer,
        StructureRepository $structureRepository
    ): Response {
        return $this->apiJsonResponse(
            $structureRepository->retrieveAll(),
            Response::HTTP_OK,
            $this->getLevel($request),
            $serializer
        );
    }

    /**
     * @Route("/site/{id}", name="structure_site_update", methods="PATCH")
     *
     * @SWG\Parameter(
     *     description="Id of the Site",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Site fields",
     *     type="json",
     *     required=true,
     *    @Model(type=Site::class, groups={"updatable"})
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Updated Site",
     *     @Model(type=Site::class, groups={"full"})
     * )
     * @SWG\Tag(name="Site")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param SiteManager $siteManager
     * @param string $id
     *
     * @return Response
     */
    public function updateSite(
        Request $request,
        EntitySerializerInterface $serializer,
        ValidatorInterface $validator,
        SiteManager $siteManager,
        string $id
    ): Response {
        try {
            /** @var SiteEdit $siteDto */
            $siteDto = $serializer->deserialize($request->getContent(), SiteEdit::class, 'json');
            $validation = $validator->validate($siteDto);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            /** @var Site $site */
            $site = $siteManager->getUpdatedEntity($siteDto, $id);
            $validation = $validator->validate($site);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            $savedEntity = $siteManager->save($site);
        } catch (NotFoundHttpException | ConflictException $exception) {
            return $this->apiJsonResponse($exception->getMessage(), $exception->getStatusCode());
        } catch (ValidationException $exception) {
            return $this->apiJsonResponse($exception->getMessage(), Response::HTTP_CONFLICT);
        }

        return $this->apiJsonResponse($savedEntity, Response::HTTP_OK, $this->getLevel($request), $serializer);
    }
}
