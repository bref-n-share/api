<?php

namespace App\Application\Controller\User;

use App\Application\Exception\ConflictException;
use App\Application\Exception\ValidationException;
use App\Domain\Core\Serializer\EntitySerializerInterface;
use App\Domain\Structure\Manager\StructureManagerInterface;
use App\Domain\User\Entity\Member;
use App\Domain\User\Manager\UserManagerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/user/member", name="user_member_create", methods="POST")
     *
     * @param Request $request
     * @param EntitySerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param StructureManagerInterface $structureManager
     * @param UserManagerInterface $userManager
     *
     * @return Response
     *
     * @throws ValidationException
     */
    public function create(
        Request $request,
        EntitySerializerInterface $serializer,
        ValidatorInterface $validator,
        StructureManagerInterface $structureManager,
        UserManagerInterface $userManager
    ): Response {
        try {
            /** @var Member $member */
            $member = $serializer->deserialize($request->getContent(), Member::class, 'json');

            if (!$member->getStructure()) {
                $requestBody = json_decode($request->getContent(), 'true');

                if (!isset($requestBody['structure_id'])) {
                    throw new ConflictException('The option \'structure_id\' must be defined');
                }

                $member->setStructure($structureManager->retrieve($requestBody['structure_id']));
            }

            $validation = $validator->validate($member);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            $entity = $userManager->create($member);
        } catch (NotFoundHttpException | ConflictException $exception) {
            return $this->json($exception->getMessage(), $exception->getCode());
        } catch (UniqueConstraintViolationException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_CONFLICT);
        }

        return $this->json($entity, Response::HTTP_CREATED);
    }
}
