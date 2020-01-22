<?php

namespace App\Infrastructure\Serializer;

use App\Domain\Core\Serializer\EntitySerializerInterface;
use App\Infrastructure\Serializer\Normalizer\EntityNormalizer;
use App\Infrastructure\Serializer\Normalizer\UuidDenormalizer;
use App\Infrastructure\Serializer\Normalizer\UuidNormalizer;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorFromClassMetadata;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EntitySerializer implements EntitySerializerInterface
{
    private Serializer $serializer;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

        $discriminator = new ClassDiscriminatorFromClassMetadata($classMetadataFactory);

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
            AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT
        ];

        $this->serializer = new Serializer(
            [
                new DateTimeNormalizer(),
                new UuidNormalizer(),
                new UuidDenormalizer(),
                new EntityNormalizer(
                    $entityManager,
                    $classMetadataFactory,
                    null,
                    null,
                    new ReflectionExtractor(),
                    $discriminator,
                    $defaultContext
                ),
                new ObjectNormalizer(
                    $classMetadataFactory,
                    null,
                    null,
                    new ReflectionExtractor(),
                    $discriminator,
                    null,
                    $defaultContext
                )
            ],
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

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $entity = $this->serializer->denormalize($data, $type, $format, $context);

        if (!$entity) {
            throw new NotFoundHttpException($type . ' not found with id ' . $data);
        }

        return $entity;
    }
}
