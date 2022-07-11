<?php

namespace Artisen2021\LinkedInSDK\Http;

use Artisen2021\LinkedInSDK\Authentication\Client;
use Artisen2021\LinkedInSDK\Exception\CouldNotCreateAnalytics;
use Artisen2021\LinkedInSDK\UrlEnums;
use GuzzleHttp\Exception\RequestException;

class AnalyticsRequest extends LinkedInRequest
{
    use TraitToken;

    private const BEARER = 'Bearer ';
    public Client $client;
    public string $token;
    public const CAMPAIGN_METRIC_FIELDS = [
        'clicks',
        'approximateUniqueImpressions',
        'impressions',
        'costInLocalCurrency',
        'externalWebsiteConversions',
        'pivotValue',
    ];

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->token = $this->getTokenCode();
    }

    public function fetchAnalytics(string $dateRange, string $granularity, int $campaignGroupId)
    {
        $url = sprintf(
            'q=analytics&pivot=CAMPAIGN_GROUP&%s&timeGranularity=%s&fields[0]=%s&campaignGroups[0]=urn:li:sponsoredCampaignGroup:%s',
            $dateRange,
            $granularity,
            implode(',',self::CAMPAIGN_METRIC_FIELDS),
            $campaignGroupId,
        );

        $uri = rtrim($this->client->buildUrl(UrlEnums::URL['AD_ANALYTICS'].'?'. $url,[]),'?');

        $header = [
            'Authorization' => self::BEARER . $this->token,
            'Content-type' => 'application/json'
        ];

        try {
            $request = new LinkedInRequest();
            $response = $request->send('GET', $uri, $header, []);
        } catch (RequestException $e) {
            throw new CouldNotCreateAnalytics($e->getMessage(), $e->getCode());
        }
        return json_decode($response->getBody()->getContents(), true);
    }



}
