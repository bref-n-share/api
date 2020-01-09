<?php

namespace App\Domain\Core\Serializer;

interface EntitySerializerInterface
{
    /**
     * Serializes data in the appropriate format.
     *
     * @param mixed  $data    Any data
     * @param string $format  Format name
     * @param array  $context Options normalizers/encoders have access to
     *
     * @return string
     */
    public function serialize($data, string $format, array $context = []): string;

    /**
     * Deserializes data into the given type.
     *
     * @param mixed $data
     * @param string $type
     * @param string $format
     * @param array $context
     *
     * @return object|array
     */
    public function deserialize($data, string $type, string $format, array $context = []);
}
