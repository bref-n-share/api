<?php
namespace App\Application\Controller\Notification;

use App\Application\Controller\RestAPIController;
use App\Application\Exception\ValidationException;
use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Serializer\EntitySerializerInterface;
use App\Domain\Notification\DTO\PostNotificationCreate;
use App\Domain\Notification\DTO\SimpleNotificationCreate;
use App\Domain\Notification\Manager\NotificationManager;
use App\Domain\Notification\Manager\PostNotificationManager;
use App\Domain\Notification\Manager\SimpleNotificationManager;
use App\Domain\Post\Manager\PostManager;
use App\Domain\User\Entity\Donor;
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
     * @Route("/post/{id}", name="notification_post", methods="POST")
     *
     * @SWG\Parameter(
     *     description="Id of the Post",
     *     name="id",
     *     in="path",
     *     type="string",
     *     @Model(type=Ramsey\Uuid\UuidInterface::class)
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="PostNotification fields",
     *     type="json",
     *     required=true,
     *     @Model(type=PostNotificationCreate::class)
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Created Post Notification",
     *     @Model(type="App\Domain\Notification\Entity\PostNotification", groups={"essential"})
     * )
     * @SWG\Tag(name="Notification")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param PostNotificationManager $notificationManager
     * @param PostManager $postManager
     * @param string $id
     *
     * @return Response
     */
    public function createPostNotification(
        Request $request,
        EntitySerializerInterface $serializer,
        ValidatorInterface $validator,
        PostNotificationManager $notificationManager,
        PostManager $postManager,
        string $id
    ): Response {
        try {
            /** @var PostNotificationCreate $postNotification */
            $postNotification = $serializer->deserialize(
                $request->getContent(),
                PostNotificationCreate::class,
                'json'
            );

            $validation = $validator->validate($postNotification);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            $post = $postManager->retrieve($id);

            $this->denyAccessUnlessGranted('notify', $post);

            $notification = $notificationManager->create($post, $postNotification);
        } catch (NotFoundHttpException | ConflictException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        } catch (ValidationException $exception) {
            return $this->apiJsonResponse($this->formatErrorMessage($exception->getMessage()), Response::HTTP_CONFLICT);
        }

        return $this->apiJsonResponse($notification, Response::HTTP_CREATED, $this->getLevel($request), $serializer);
    }

    /**
     * @Route("/simple", name="notification_simple", methods="POST")
     *
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="SimpleNotification fields",
     *     type="json",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="title", type="string", example="title"),
     *         @SWG\Property(property="description", type="string", example="description"),
     *         @SWG\Property(property="expirationDate", type="string", example="2020-01-21T22:33:45.307Z"),
     *         @SWG\Property(property="site", type="string", example="a2172105-91b0-4f84-88a5-a62254243927")
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Created Simple Notification",
     *     @Model(type="App\Domain\Notification\Entity\SimpleNotification", groups={"essential"})
     * )
     * @SWG\Tag(name="Notification")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param SimpleNotificationManager $notificationManager
     *
     * @return Response
     */
    public function createSimpleNotification(
        Request $request,
        EntitySerializerInterface $serializer,
        ValidatorInterface $validator,
        SimpleNotificationManager $notificationManager
    ): Response {
        try {
            /** @var SimpleNotificationCreate $simpleNotificationDto */
            $simpleNotificationDto = $serializer->deserialize(
                $request->getContent(),
                SimpleNotificationCreate::class,
                'json'
            );

            $validation = $validator->validate($simpleNotificationDto);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            $this->denyAccessUnlessGranted('notify', $simpleNotificationDto);

            $notification = $notificationManager->create($simpleNotificationDto);
        } catch (NotFoundHttpException | ConflictException $exception) {
            return $this->apiJsonResponse(
                $this->formatErrorMessage($exception->getMessage()),
                $exception->getStatusCode()
            );
        } catch (ValidationException $exception) {
            return $this->apiJsonResponse($this->formatErrorMessage($exception->getMessage()), Response::HTTP_CONFLICT);
        }

        return $this->apiJsonResponse($notification, Response::HTTP_CREATED, $this->getLevel($request), $serializer);
    }

    /**
     * @Route(name="/notification", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="All Favorite Notifications",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type="App\Domain\Notification\Entity\Notification", groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Notification")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param NotificationManager $notificationManager
     *
     * @return Response
     */
    public function getAll(
        Request $request,
        EntitySerializerInterface $serializer,
        NotificationManager $notificationManager
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
            $notificationManager->getValidNotifications($user->getSites()->getValues()),
            Response::HTTP_OK,
            $this->getLevel($request),
            $serializer
        );
    }
}
