<?php
declare(strict_types=1);

namespace Artisen2021\LinkedInSDK\Http;

use Artisen2021\LinkedInSDK\Authentication\AccessToken;
use Artisen2021\LinkedInSDK\Authentication\Client;
use Artisen2021\LinkedInSDK\Distribution\CampaignGroup;
use Artisen2021\LinkedInSDK\Exception\CouldNotCreateACampaignGroup;
use Artisen2021\LinkedInSDK\Exception\CouldNotDeleteACampaignGroup;
use Artisen2021\LinkedInSDK\Exception\CouldNotUpdateACampaignGroup;
use Artisen2021\LinkedInSDK\UrlEnums;
use GuzzleHttp\Exception\RequestException;

class CampaignGroupRequest extends LinkedInRequest
{
    use TraitOAuthIsSetUp;

    //TODO: may be good to make request headers enums?
    private const BEARER = 'Bearer ';
    public const HEADER_RESOURCE_ID = 'X-LinkedIn-Id';
    public Client $client;
    public string $token;

    public function __construct(Client $client, string $token)
    {
        $this->client = $client;
        $this->token = $token;
    }

    /**
     * @throws CouldNotCreateACampaignGroup
     */
    public function create(array $parameters)
    {
        $uri = $this->client->buildUrl(UrlEnums::URL['AD_CAMPAIGN_GROUPS'], []);

        $header = [
            'Authorization' => self::BEARER . $this->token,
            'Content-type' => 'application/json'
        ];

        try {
            $request = new LinkedInRequest();
            $response = $request->send('POST', $uri, $header, $parameters);
        } catch (RequestException $e) {
            throw new CouldNotCreateACampaignGroup($e->getMessage(), $e->getCode());
        }
        $externalId = $response->getHeaderLine(self::HEADER_RESOURCE_ID);

        if (empty($externalId)) {
            throw new CouldNotCreateACampaignGroup('LinkedIn : Failed to create a campaign group because of empty external id');
        }

        $campaignGroup = new CampaignGroup();
        $campaignGroup->setExternalId((int) $externalId);

        return $campaignGroup;
    }

    /**
     * @throws CouldNotDeleteACampaignGroup
     */
    public function delete(int $campaignGroupId): void
    {
        $parameters = [
            'patch' => [
                '$set' => [
                    'status' => 'ARCHIVED',
                ]
            ]
        ];
        $uri = $this->client->buildUrl(UrlEnums::URL['AD_CAMPAIGN_GROUPS'].'/'.$campaignGroupId, []);
        $header = ['Authorization' => self::BEARER . $this->token];
        try {
            (new LinkedInRequest())->send('POST', $uri, $header, $parameters);
        } catch (RequestException $e) {
            throw new CouldNotDeleteACampaignGroup($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @throws CouldNotUpdateACampaignGroup
     */
    public function update(int $campaignGroupId, array $params): void
    {
        //$params['external_id'] = $campaignGroupId;
        $parameters = [
            'patch' => [
                '$set' => $params,
            ],
        ];
        $uri = $this->client->buildUrl(UrlEnums::URL['AD_CAMPAIGN_GROUPS'].'/'.$campaignGroupId, []);
        $header = [
            'Authorization' => 'Bearer ' . $this->token,
            'Content-type' => 'application/json'
        ];
        try {
            (new LinkedInRequest())->send('POST', $uri, $header, $parameters);
        } catch (RequestException $e) {
            throw new CouldNotUpdateACampaignGroup($e->getMessage(), $e->getCode());
        }
    }

}
