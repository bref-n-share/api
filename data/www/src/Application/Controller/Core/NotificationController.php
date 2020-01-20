<?php

namespace App\Application\Controller\Core;

use App\Application\Controller\RestAPIController;
use App\Application\Exception\ValidationException;
use App\Domain\Core\DTO\CustomSocialNetworkNotificationDto;
use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Manager\NotificationManager;
use App\Domain\Core\Serializer\EntitySerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/v1/notification")
 */
class NotificationController extends RestAPIController
{
    /**
     * @Route("/publish", name="notification_custom_publish", methods="POST")
     *
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Notification fields",
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
     *     description="Created Notification",
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
     * @SWG\Tag(name="Notification")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param NotificationManager $notificationManager
     *
     * @return Response
     */
    public function createNotification(
        Request $request,
        EntitySerializerInterface $serializer,
        ValidatorInterface $validator,
        NotificationManager $notificationManager
    ): Response {
        try {
            $user = $this->getUser();

            /** @var CustomSocialNetworkNotificationDto $notification */
            $notification = $serializer->deserialize(
                $request->getContent(),
                CustomSocialNetworkNotificationDto::class,
                'json'
            );

            $this->denyAccessUnlessGranted('publish', $notification);

            $notification->setStructure($user->getStructure());

            $validation = $validator->validate($notification);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            $notificationManager->publish($notification, $notification->getChannels());
        } catch (NotFoundHttpException | ConflictException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        } catch (ValidationException $exception) {
            return $this->apiJsonResponse($this->formatErrorMessage($exception->getMessage()), Response::HTTP_CONFLICT);
        }

        return $this->apiJsonResponse('', Response::HTTP_OK, $this->getLevel($request), $serializer);
    }
}
