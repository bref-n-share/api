<?php

namespace App\Application\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController
{
    /**
     * @Route("/test", name="test")
     */
    public function number()
    {
        return new JsonResponse(['a' => 'test']);
    }
}
