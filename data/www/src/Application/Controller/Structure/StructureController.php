<?php

namespace App\Application\Controller\Structure;

use App\Application\Controller\RestAPIController;
use App\Application\Exception\ValidationException;
use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Serializer\EntitySerializerInterface;
use App\Domain\Structure\DTO\SiteEdit;
use App\Domain\Structure\Entity\Site;
use App\Domain\Structure\Entity\Organization;
use App\Domain\Structure\Manager\OrganizationManager;
use App\Domain\Structure\Manager\SiteManager;
use App\Domain\Structure\Repository\OrganizationRepositoryInterface;
use App\Domain\Structure\Repository\SiteRepositoryInterface;
use App\Domain\Structure\Repository\StructureRepository;
use App\Domain\User\Entity\Donor;
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
     * @Route("/site", name="structure_site_get_all", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="All Site",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type="App\Domain\Structure\Entity\Site", groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Site")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param SiteRepositoryInterface $repository
     *
     * @return Response
     */
    public function getAllSite(
        Request $request,
        EntitySerializerInterface $serializer,
        SiteRepositoryInterface $repository
    ): Response {
        return $this->apiJsonResponse(
            $repository->retrieveAll(),
            Response::HTTP_OK,
            $this->getLevel($request),
            $serializer
        );
    }

    /**
     * @Route("/site/favorite", name="structure_site_get_all_favorite", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="All Favorite Sites",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type="App\Domain\Structure\Entity\Site", groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Site")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     *
     * @return Response
     */
    public function getAllFavorite(
        Request $request,
        EntitySerializerInterface $serializer
    ): Response {

        $user = $this->getUser();

        try {
            if (!($user instanceof Donor)) {
                throw new ConflictException('Must be a Donor');
            }
        } catch (ConflictException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        }

        return $this->apiJsonResponse(
            $user->getSites(),
            Response::HTTP_OK,
            $this->getLevel($request),
            $serializer
        );
    }

    /**
     * @Route("/organization", name="structure_organization_get_all", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="All Organization",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type="App\Domain\Structure\Entity\Organization", groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Organization")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param OrganizationRepositoryInterface $repository
     *
     * @return Response
     */
    public function getAllOrganization(
        Request $request,
        EntitySerializerInterface $serializer,
        OrganizationRepositoryInterface $repository
    ): Response {
        return $this->apiJsonResponse(
            $repository->retrieveAll(),
            Response::HTTP_OK,
            $this->getLevel($request),
            $serializer
        );
    }

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

            /** @var Site $entityToSave */
            $entityToSave = $siteManager->retrieve($id);

            $this->denyAccessUnlessGranted('update', $entityToSave);

            /** @var Site $site */
            $site = $siteManager->getUpdatedEntity($siteDto, $entityToSave);
            $validation = $validator->validate($site);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            $savedEntity = $siteManager->save($site);
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
     * @Route("/site/{id}", name="structure_site_get", methods="GET")
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
     *     description="Requested site",
     *     @Model(type=Site::class, groups={"full"})
     * )
     * @SWG\Tag(name="Site")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param string $id
     * @param SiteManager $siteManager
     *
     * @return Response
     */
    public function getOneSite(
        Request $request,
        EntitySerializerInterface $serializer,
        string $id,
        SiteManager $siteManager
    ): Response {
        try {
            $site = $siteManager->retrieve($id);
        } catch (NotFoundHttpException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        }

        return $this->apiJsonResponse($site, Response::HTTP_OK, $this->getLevel($request), $serializer);
    }

    /**
     * @Route("/organization/{id}", name="structure_organization_get", methods="GET")
     *
     * @SWG\Parameter(
     *     description="Id of the Organization",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Requested oragnization",
     *     @Model(type=Organization::class, groups={"full"})
     * )
     * @SWG\Tag(name="Organization")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param string $id
     * @param OrganizationManager $organizationManager
     *
     * @return Response
     */
    public function getOneOrganization(
        Request $request,
        EntitySerializerInterface $serializer,
        string $id,
        OrganizationManager $organizationManager
    ): Response {
        try {
            $organization = $organizationManager->retrieve($id);
        } catch (NotFoundHttpException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        }

        return $this->apiJsonResponse($organization, Response::HTTP_OK, $this->getLevel($request), $serializer);
    }
}
