<?php

namespace App\Infrastructure\Serializer\Normalizer;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UuidDenormalizer implements DenormalizerInterface
{
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (null === $data) {
            return null;
        }

        return Uuid::fromString($data);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return (Uuid::class === $type || UuidInterface::class === $type) && $this->isValid($data);
    }

    private function isValid($data)
    {
        return $data === null || (is_string($data) && Uuid::isValid($data));
    }
}
