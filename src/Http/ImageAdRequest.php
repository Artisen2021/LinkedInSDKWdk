<?php
declare(strict_types=1);

namespace Artisen2021\LinkedInSDK\Http;

use Artisen2021\LinkedInSDK\Authentication\AccessToken;
use Artisen2021\LinkedInSDK\Authentication\Client;
use Artisen2021\LinkedInSDK\Builder\AdRequestBodyBuilder;
use Artisen2021\LinkedInSDK\Distribution\ImageAd;
use Artisen2021\LinkedInSDK\Exception\CouldNotCreateAnImageAd;
use Artisen2021\LinkedInSDK\UrlEnums;
use GuzzleHttp\Exception\RequestException;

class ImageAdRequest extends LinkedInRequest
{
    use TraitOAuthIsSetUp;

    private const BEARER = 'Bearer ';
    public const HEADER_RESOURCE_ID = 'X-LinkedIn-Id';
    public Client $client;
    public string $token;
    public AdRequestBodyBuilder $builder;

    public function __construct()
    {
        $this->client = $this->getSetUpClient();
        $this->token = $this->getSetUpToken();
    }

    public function create(array $parameters)
    {
        $darkShare = $this->createDarkShare($parameters);

        $requestBody = $this->builder->createImageAdRequest([
            'campaign_id' => $parameters['campaign_id'],
            'activity' => $darkShare['activity'],
            'share_reference' => $darkShare['id'],
        ]);

        $uri = $this->client->buildUrl(UrlEnums::URL['AD_CREATIVES'], $requestBody);

        $header = ['Authorization' => self::BEARER . $this->token];

        try {
            $request = new LinkedInRequest();
            $response = $request->send('POST', $uri, $header, []);
        } catch (RequestException $e) {
            throw new CouldNotCreateAnImageAd($e->getMessage(), $e->getCode());
        }
        $externalId = $response->getHeaderLine(self::HEADER_RESOURCE_ID);

        if (empty($externalId)) {
            throw new CouldNotCreateAnImageAd('LinkedIn : Failed to create an image ad because of empty external id');
        }
        $imageAd = new ImageAd();
        $imageAd->setExternalId((int) $externalId);

        return $imageAd;
    }

    private function createDarkShare(array $params): array
    {
        $requestBody = json_encode($this->builder->createDarkShareForImageAd([
            'account_id' => $params['account_id'],
            'page_id' => $params['linkedin_page_id'],
            'campaign_id' => $params['campaign_id'],
            'type' => $params['media_type'],
            'text' => $params['message'],
            'title' => $params['headline'],
            'landing_page_url' => $params['landing_page_url'],
            'media_url' => $params['media_url'],
            'call_to_action' => $params['call_to_action'],
        ]));

        $uri = $this->client->buildUrl(UrlEnums::URL['SHARES'], $requestBody);

        $header = ['Authorization' => 'Bearer ' . $this->token];

        try {
            $request = new LinkedInRequest();
            $response = $request->send('POST', $uri, $header, []);
        } catch (RequestException $e) {
            throw new CouldNotCreateAnImageAd($e->getMessage(), $e->getCode());
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}