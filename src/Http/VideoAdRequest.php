<?php
declare(strict_types=1);

namespace Artisen2021\LinkedInSDK\Http;

use Artisen2021\LinkedInSDK\Authentication\AccessToken;
use Artisen2021\LinkedInSDK\Authentication\Client;
use Artisen2021\LinkedInSDK\Builder\AdBuilder;
use Artisen2021\LinkedInSDK\Distribution\VideoAd;
use Artisen2021\LinkedInSDK\Exception\CouldNotCreateAnAd;
use Artisen2021\LinkedInSDK\Exception\CouldNotCreateAnImageAd;
use Artisen2021\LinkedInSDK\Exception\CouldNotCreateAVideoAd;
//undefined classes?
use Artisen2021\LinkedInSDK\Exception\CouldNotCreateDarkShare;
use Artisen2021\LinkedInSDK\Exception\CouldNotCreateDirectSponsoredContent;
use Artisen2021\LinkedInSDK\Exception\CouldNotUploadVideoAd;
use Artisen2021\LinkedInSDK\UrlEnums;
use GuzzleHttp\Exception\RequestException;

class VideoAdRequest extends LinkedInRequest
{
    use TraitToken;

    private const BEARER = 'Bearer ';
    public const HEADER_RESOURCE_ID = 'X-LinkedIn-Id';
    public Client $client;
    public string $token;
    public VideoUploaderRequest $videoUploaderRequest;
    public AdBuilder $builder;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->token = $this->getTokenCode();
        $this->videoUploaderRequest = new VideoUploaderRequest($this->client);
        $this->builder = new AdBuilder();
    }

    /**
     * @throws CouldNotCreateAVideoAd
     * @throws CouldNotUploadVideoAd
     */
    public function create(array $parameters): VideoAd
    {
        $mediaAsset = $this->videoUploaderRequest->uploadVideo($parameters);

        $darkShare = $this->createDarkShare($parameters, $mediaAsset);

        $directShareId = $this->createDirectSponsoredContent($parameters, $darkShare['id']);

        $requestBody = $this->builder->createVideoAdRequest([
            'campaign_id' => $parameters['campaign_id'],
            'direct_share_reference' => $directShareId,
        ]);

        $uri = $this->client->buildUrl(UrlEnums::URL['AD_CREATIVES'], []);

        $header = ['Authorization' => self::BEARER . $this->token];

        try {
            $request = new LinkedInRequest();
            $response = $request->send('POST', $uri, $header, $requestBody);
        } catch (RequestException $e) {
            throw new CouldNotCreateAVideoAd($e->getMessage(), $e->getCode());
        }
        $externalId = $response->getHeaderLine(self::HEADER_RESOURCE_ID);

        if (empty($externalId)) {
            throw new CouldNotCreateAVideoAd('LinkedIn : Failed to create a video ad because of empty external id');
        }
        $videoAd = new VideoAd();
        $videoAd->setExternalId((int) $externalId);

        return $videoAd;
    }

    private function createDarkShare(array $params, string $mediaAsset): array
    {
        $requestBody = $this->builder->createDarkShareForVideoAd([
            'page_id' => $params['linkedin_page_id'],
            'type' => $params['media_type'],
            'text' => $params['message'],
            'title' => $params['headline'],
            'landing_page_url' => $params['landing_page_url'],
            'media' => $mediaAsset,
            'call_to_action' => $params['call_to_action'],
        ]);

        $uri = rtrim($this->client->buildUrl(UrlEnums::URL['UGC_POST'],[]), '?');

        $header = ['Authorization' => self::BEARER . $this->token];

        try {
            $request = new LinkedInRequest();
            $response = $request->send('POST', $uri, $header, $requestBody);
        } catch (RequestException $e) {
            throw new CouldNotCreateAnImageAd('LinkedIn : Failed to create a DarkShare for a video ad', $e, $params);
        }
        return json_decode($response->getBody()->getContents(), true);
    }

    private function createDirectSponsoredContent(array $params, $ugcPostId): string
    {
        $requestBody = $this->builder->createAdDirectSponsoredContent([
            'account_id' => $params['account_id'],
            'page_id' => $params['linkedin_page_id'],
            'post_reference' => $ugcPostId,
            'title' => $params['headline'],
        ]);

        $uri = $this->client->buildUrl(UrlEnums::URL['DIRECT_SPONSORED_POST'], []);

        $header = ['Authorization' => 'Bearer ' . $this->token];

        try {
            $request = new LinkedInRequest();
            $response = $request->send('POST', $uri, $header, $requestBody);
        } catch (RequestException $e) {
            //TODO: $e is cast to int somewhere?
            throw new CouldNotCreateAnImageAd('LinkedIn : Failed to create a direct sponsored content for a video ad', $e, $params);
        }

        $externalId = $response->getHeaderLine(self::HEADER_RESOURCE_ID);
        if (empty($externalId)) {
            throw new CouldNotCreateAnAd(
                'LinkedIn : Failed to create a direct sponsored content for a video ad because of empty external id'
            );
        }
        return $externalId;
    }
}
