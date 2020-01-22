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

    public function getUpdatedEntity(SiteEdit $siteDto, Site $site): Site
    {
        return $site
            ->setAddress($siteDto->getAddress() ?? $site->getAddress())
            ->setPostalCode($siteDto->getPostalCode() ?? $site->getPostalCode())
            ->setCity($siteDto->getCity() ?? $site->getCity())
            ->setPhone($siteDto->getPhone() ?? $site->getPhone())
            ->setDescription($siteDto->getDescription() ?? $site->getDescription())
            ->setLongitude($siteDto->getLongitude() ?? $site->getLongitude())
            ->setLatitude($siteDto->getLatitude() ?? $site->getLatitude())
        ;
    }
}
