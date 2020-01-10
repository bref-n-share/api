<?php

namespace App\Infrastructure\Describer;

use Doctrine\Common\Annotations\Reader;
use EXSyst\Component\Swagger\Schema;
use Nelmio\ApiDocBundle\Describer\ModelRegistryAwareInterface;
use Nelmio\ApiDocBundle\Describer\ModelRegistryAwareTrait;
use Nelmio\ApiDocBundle\Model\Model;
use Nelmio\ApiDocBundle\ModelDescriber\Annotations\AnnotationsReader;
use Nelmio\ApiDocBundle\ModelDescriber\ModelDescriberInterface;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

class UuidDescriber implements ModelDescriberInterface, ModelRegistryAwareInterface
{
    use ModelRegistryAwareTrait;

    private PropertyInfoExtractorInterface $propertyInfo;

    private Reader $doctrineReader;

    public function __construct(
        PropertyInfoExtractorInterface $propertyInfo,
        Reader $reader
    ) {
        $this->propertyInfo = $propertyInfo;
        $this->doctrineReader = $reader;
    }

    public function describe(Model $model, Schema $schema)
    {
        $schema->setType('string');
        $schema->setExample('a2172105-91b0-4f84-88a5-a62254243927');
    }

    public function supports(Model $model): bool
    {
        return Type::BUILTIN_TYPE_OBJECT === $model->getType()->getBuiltinType()
            && $model->getType()->getClassName() === UuidInterface::class
        ;
    }
}
