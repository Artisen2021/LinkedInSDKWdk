<?php

namespace Artisen2021\LinkedInSDK\Http;

use Artisen2021\LinkedInSDK\Authentication\Client;
use Artisen2021\LinkedInSDK\Builder\AdBuilder;
use Artisen2021\LinkedInSDK\Exception\CouldNotUploadVideoAd;
use Artisen2021\LinkedInSDK\UrlEnums;
use GuzzleHttp\Exception\RequestException;

class VideoUploaderRequest extends LinkedInRequest
{
    use TraitToken;

    private const BEARER = 'Bearer ';
    public Client $client;
    public string $token;
    public AdBuilder $builder;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->token = $this->getTokenCode();
        $this->builder = new AdBuilder();
    }

    /**
     * @throws CouldNotUploadVideoAd
     */
    public function uploadVideo(array $parameters): string
    {
        $uploadRequest = $this->requestCredentialsForVideoUpload($parameters['linkedin_page_id']);

        $uploadUrl = $uploadRequest['value']
        ['uploadMechanism']
        ['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']
        ['uploadUrl'];
        $uploadHeaders = $uploadRequest['value']
        ['uploadMechanism']
        ['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']
        ['headers'];

        $mediaAsset = $uploadRequest['value']['asset'];

        try {
            (new LinkedInRequest())
                ->send('PUT', $uploadUrl, $uploadHeaders, file_get_contents($parameters['media_url']));
        } catch (RequestException $e) {
            throw new CouldNotUploadVideoAd('LinkedIn : Failed to upload video', $e, $parameters);
        }
        return $mediaAsset;
    }

    private function requestCredentialsForVideoUpload(string $pageId): array
    {
        $requestBody = $this->builder->requestCredentialsForVideoUpload($pageId);

        $header = [
            'Authorization' => self::BEARER . $this->token,
            'Content-type' => 'application/json'
        ];

        $uri = rtrim($this->client->buildUrl(UrlEnums::URL['ASSET_REGISTER'],[]), '?');

        try {
            $request = new LinkedInRequest();
            $response = $request->send('POST', $uri, $header, $requestBody);
        } catch (RequestException $e) {
            throw new CouldNotUploadVideoAd('LinkedIn : Failed to request credentials for video upload', $e, ['ad_id' => $pageId]);
        }
        return json_decode($response->getBody()->getContents(), true);
    }
}
