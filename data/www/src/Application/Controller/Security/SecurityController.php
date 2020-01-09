<?php

namespace App\Application\Controller\Security;

use App\Application\Controller\RestAPIController;
use App\Domain\Core\Serializer\EntitySerializerInterface;
use App\Domain\User\DTO\AuthenticateUserDTO;
use App\Domain\User\Manager\SecurityManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/security")
 **/
class SecurityController extends RestAPIController
{
    /**
     * @Route("/authenticate", name="security_request_authenticate", methods="GET")
     *
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Request fields",
     *     type="json",
     *     required=true,
     *    @Model(type="App\Domain\User\DTO\AuthenticateUserDTO")
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Token",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="token", type="string")
     *     )
     * )
     *
     * @SWG\Tag(name="Security")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param SecurityManager $securityManager
     *
     * @return Response
     */
    public function getToken(
        Request $request,
        EntitySerializerInterface $serializer,
        SecurityManager $securityManager
    ): Response {
        try {
            /** @var AuthenticateUserDTO $authenticateUserDTO */
            $authenticateUserDTO = $serializer->deserialize($request->getContent(), AuthenticateUserDTO::class, 'json');

            $token = $securityManager->getUserToken($authenticateUserDTO);
        } catch (NotFoundHttpException | AccessDeniedHttpException $exception) {
            return $this->apiJsonResponse($exception->getMessage(), $exception->getStatusCode());
        }

        $data = [
            'token' => $token,
        ];

        return $this->apiJsonResponse($data, Response::HTTP_OK, $this->getLevel($request), $serializer);
    }

    /**
     * @Route("/test", name="security_request_test", methods="GET")
     *
     * @SWG\Parameter(
     *     in="header",
     *     name="X-AUTH-TOKEN",
     *     type="string",
     *     description="token",
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Token valid"
     * )
     *
     * @SWG\Tag(name="Security")
     *
     * @return Response
     */
    public function testToken(): Response {
        return new Response();
    }
}
