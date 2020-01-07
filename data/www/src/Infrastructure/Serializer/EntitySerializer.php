<?php

namespace App\Infrastructure\Serializer;

use App\Domain\Core\Serializer\EntitySerializerInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorFromClassMetadata;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EntitySerializer implements EntitySerializerInterface
{
    private Serializer $serializer;

    public function __construct()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

        $discriminator = new ClassDiscriminatorFromClassMetadata($classMetadataFactory);

        $this->serializer = new Serializer(
            [new ObjectNormalizer($classMetadataFactory, null, null, new ReflectionExtractor(), $discriminator)],
            ['json' => new JsonEncoder()]
        );
    }

    public function serialize($data, string $format, array $context = []): string
    {
        return $this->serializer->serialize($data, $format, $context);
    }

    public function deserialize($data, string $type, string $format, array $context = [])
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }
}
