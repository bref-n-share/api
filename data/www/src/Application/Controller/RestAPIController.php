<?php

namespace App\Application\Controller;

use App\Domain\Core\Serializer\EntitySerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class RestAPIController extends AbstractController
{
    private const REQUEST_LEVEL_KEY = 'level';
    private const DEFAULT_LEVEL     = 'essential';
    private const DEFAULT_DELIMITER   = '_';

    /**
     * Get the requested data levels from the current request.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getLevel(Request $request): array
    {
        return $request->get(self::REQUEST_LEVEL_KEY) ?
            explode(self::DEFAULT_DELIMITER, $request->get(self::REQUEST_LEVEL_KEY)) : [self::DEFAULT_LEVEL];
    }

    /**
     * This method uses the custom serializer if this one is passed in parameter
     * and returns the serialized data with corresponding levels.
     *
     * If no serializer is passed in parameter, the symfony serializer will be used
     * thanks to the $this->json() method.
     *
     * @param null                           $data
     * @param int                            $status
     * @param array                          $levels Levels of data information
     * @param EntitySerializerInterface|null $serializer
     * @param array                          $headers
     *
     * @return JsonResponse
     */
    protected function apiJsonResponse(
        $data = null,
        int $status = Response::HTTP_OK,
        array $levels = [],
        EntitySerializerInterface $serializer = null,
        array $headers = []
    ): JsonResponse {
        if (!$serializer) {
            return $this->json($data, $status, $headers, ['groups' => $levels]);
        }

        return new JsonResponse(
            $serializer->serialize($data, 'json', ['groups' => $levels]),
            $status,
            $headers,
            true
        );
    }

    protected function formatErrorMessage(string $errorMessage): array
    {
        return ['message' => $errorMessage];
    }

    protected function formatQueryParameters(array $parameters): array
    {
        $options = [];
        foreach ($parameters as $key => $value) {
            $options[$key] = explode(self::DEFAULT_DELIMITER, $value);
        }

        return $options;
    }
}
