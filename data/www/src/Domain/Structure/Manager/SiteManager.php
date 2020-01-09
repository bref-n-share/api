<?php

namespace App\Domain\Structure\Manager;

use App\Domain\Structure\DTO\SiteEdit;
use App\Domain\Structure\Entity\Site;
use App\Domain\Structure\Entity\Structure;

class SiteManager extends AbstractStructureManager
{
    public function supports(Structure $structure): bool
    {
        return $structure instanceof Site;
    }

    public function getUpdatedEntity(SiteEdit $siteDto, string $id): Site
    {
        /** @var Site $entity */
        $entity = $this->retrieve($id);

        $entity
            ->setAddress($siteDto->getAddress() === null ? $entity->getAddress() : $siteDto->getAddress())
            ->setPostalCode($siteDto->getPostalCode() === null ? $entity->getPostalCode() : $siteDto->getPostalCode())
            ->setCity($siteDto->getCity() === null ? $entity->getCity() : $siteDto->getCity())
            ->setPhone($siteDto->getPhone() === null ? $entity->getPhone() : $siteDto->getPhone())
            ->setLongitude($siteDto->getLongitude() === null ? $entity->getLongitude() : $siteDto->getLongitude())
            ->setLatitude($siteDto->getLatitude() === null ? $entity->getLatitude() : $siteDto->getLatitude())
        ;

        return $entity;
    }
}
