<?php

namespace App\Domain\Structure\Manager;

use App\Domain\Structure\Entity\Structure;

class StructureManagerChain
{
    /**
     * @var StructureManagerInterface[]
     */
    private iterable $managers;

    /**
     * StructureManagerChain constructor.
     *
     * @param iterable $managers
     */
    public function __construct(iterable $managers)
    {
        $this->managers = $managers;
    }

    public function getManager(Structure $structure): StructureManagerInterface
    {
        foreach ($this->managers as $manager) {
            if ($manager->supports($structure)) {
                return $manager;
            }
        }

        throw new \LogicException("No manager supports " . get_class($structure));
    }
}
