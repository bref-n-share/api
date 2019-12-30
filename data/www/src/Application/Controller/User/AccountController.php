<?php

namespace App\Application\Controller\User;

use App\Application\Exception\ConflictException;
use App\Application\Exception\ValidationException;
use App\Domain\User\Entity\Member;
use App\Domain\User\Manager\MemberManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/user/member", name="user_member_create", methods="POST")
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param MemberManager $memberManager
     *
     * @return Response
     *
     * @throws ValidationException
     */
    public function create(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        MemberManager $memberManager
    ): Response {
        try {
            /** @var Member $member */
            $member = $serializer->deserialize(
                $request->getContent(), Member::class, 'json');

            $validation = $validator->validate($member);

            if ($validation->count() > 0) {
                throw new ValidationException($validation);
            }

            dump($member);die;
            // $responseDto = $memberManager->create($memberDTO);
        } catch (NotFoundHttpException | ConflictException $exception) {
            return $this->json($exception->getMessage(), $exception->getCode());
        } catch (UniqueConstraintViolationException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_CONFLICT);
        }

        //return $this->json($responseDto, Response::HTTP_CREATED);
    }
}
