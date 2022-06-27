<?php
declare(strict_types=1);


namespace Artisen2021\LinkedInSDK\Distribution;

class ImageAd extends Ad
{
    protected int $externalId;

    public function setExternalId($externalId): void
    {
        $this->externalId = $externalId;
    }

    public function getExternalId(): int
    {
        return $this->externalId;
    }

}