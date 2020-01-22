<?php

namespace App\Application\Controller\Core;

use App\Application\Controller\RestAPIController;
use App\Application\Exception\ValidationException;
use App\Domain\Core\DTO\CustomSocialNetworkPublicationDto;
use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Manager\PublicationManager;
use App\Domain\Core\Serializer\EntitySerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/v1/publication")
 */
class PublicationController extends RestAPIController
{
    /**
     * @Route("/publish", name="publication_custom_publish", methods="POST")
     *
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Publication fields",
     *     type="json",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(
     *             property="channels",
     *             type="array",
     *             @SWG\Items(type="string", example="facebook")
     *         ),
     *         @SWG\Property(
     *             property="message",
     *             type="string",
     *             example="mon message"
     *         )
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Created Publication",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(
     *             property="channels",
     *             type="array",
     *             @SWG\Items(type="string", example="facebook")
     *         ),
     *         @SWG\Property(
     *             property="message",
     *             type="string",
     *             example="mon message"
     *         )
     *     )
     * )
     * @SWG\Tag(name="Publication")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param PublicationManager $publicationManager
     *
     * @return Response
     */
    public function createPublication(
        Request $request,
        EntitySerializerInterface $serializer,
        ValidatorInterface $validator,
        PublicationManager $publicationManager
    ): Response {
        try {
            $user = $this->getUser();

            /** @var CustomSocialNetworkPublicationDto $publication */
            $publication = $serializer->deserialize(
                $request->getContent(),
                CustomSocialNetworkPublicationDto::class,
                'json'
            );

            $this->denyAccessUnlessGranted('publish', $publication);

            $publication->setStructure($user->getStructure());

            $validation = $validator->validate($publication);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            $publicationManager->publish($publication, $publication->getChannels());
        } catch (NotFoundHttpException | ConflictException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        } catch (ValidationException $exception) {
            return $this->apiJsonResponse($this->formatErrorMessage($exception->getMessage()), Response::HTTP_CONFLICT);
        }

        return $this->apiJsonResponse('', Response::HTTP_NO_CONTENT, $this->getLevel($request), $serializer);
    }
}
