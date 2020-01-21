<?php
namespace App\Application\Controller\Notification;

use App\Application\Controller\RestAPIController;
use App\Application\Exception\ValidationException;
use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Serializer\EntitySerializerInterface;
use App\Domain\Notification\DTO\PostNotificationCreate;
use App\Domain\Notification\Manager\PostNotificationManager;
use App\Domain\Post\Manager\PostManager;
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
}
