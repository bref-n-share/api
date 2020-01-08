<?php

namespace App\Infrastructure\Serializer\Normalizer;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UuidNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        return $object->toString();
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof UuidInterface;
    }
}
