<?php
declare(strict_types=1);

namespace Artisen2021\LinkedInSDK\Http;

use Artisen2021\LinkedInSDK\Authentication\AccessToken;
use Artisen2021\LinkedInSDK\Authentication\Client;
use Artisen2021\LinkedInSDK\Distribution\Ad;
use Artisen2021\LinkedInSDK\Distribution\ImageAd;
use Artisen2021\LinkedInSDK\Distribution\VideoAd;
use Artisen2021\LinkedInSDK\Exception\CouldNotCreateAnAd;
use Artisen2021\LinkedInSDK\Exception\CouldNotDeleteAnAd;
use Artisen2021\LinkedInSDK\UrlEnums;
use GuzzleHttp\Exception\RequestException;

class AdRequest extends LinkedInRequest
{
    use TraitOAuthIsSetUp;

    public Client $client;
    public string $token;
    protected const MEDIA_TYPE_IMAGE = 'image';
    protected const MEDIA_TYPE_VIDEO = 'video';

    public function __construct()
    {
        $this->client = $this->getSetUpClient();
        $this->token = $this->getSetUpToken();
    }

    public function create(array $parameters)
    {
        if ($parameters['media_type'] === self::MEDIA_TYPE_IMAGE) {
            return (new ImageAdRequest())->create($parameters);
        }

        if ($parameters['media_type'] === self::MEDIA_TYPE_VIDEO) {
            return (new VideoAdRequest())->create($parameters);
        }
        throw new CouldNotCreateAnAd('LinkedIn : Failed to create an ad');
    }

    /**
     * @throws CouldNotDeleteAnAd
     */
    public function delete(int $adId): void
    {
        $uri = $this->client->buildUrl(UrlEnums::URL['AD_CREATIVES']. '/' . $adId,[]);
        $header = ['Authorization' => 'Bearer ' . $this->token];
        try {
            (new LinkedInRequest())->send('DELETE', $uri, $header, []);
        } catch (RequestException $e) {
            throw new CouldNotDeleteAnAd($e->getMessage(), $e->getCode(), ['ad_id' => $adId]);
        }
    }

    /**
     * @throws CouldNotDeleteAnAd
     */
    public function update(int $adId, array $parameters): Ad
    {
        $this->delete($adId);

        $parameters['campaign_id'] = $parameters['external_campaign_id'];
        $parameters['account_id'] = $parameters['external_account_id'];
        return $this->create($parameters);
    }

}