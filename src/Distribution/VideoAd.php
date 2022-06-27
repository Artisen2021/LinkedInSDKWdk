<?php
declare(strict_types=1);


namespace Artisen2021\LinkedInSDK\Distribution;

class VideoAd extends Ad
{
    protected int $externalId;

    public function setExternalId($externalId): void
    {
        $this->externalId = $externalId;
    }
}