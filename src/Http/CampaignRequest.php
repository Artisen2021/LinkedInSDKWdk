<?php
declare(strict_types=1);

namespace Artisen2021\LinkedInSDK\Http;

use Artisen2021\LinkedInSDK\Authentication\AccessToken;
use Artisen2021\LinkedInSDK\Authentication\Client;
use Artisen2021\LinkedInSDK\Distribution\Campaign;
use Artisen2021\LinkedInSDK\Exception\CouldNotCreateACampaign;
use Artisen2021\LinkedInSDK\Exception\CouldNotDeleteACampaign;
use Artisen2021\LinkedInSDK\Exception\CouldNotUpdateACampaign;
use Artisen2021\LinkedInSDK\UrlEnums;
use GuzzleHttp\Exception\RequestException;

class CampaignRequest extends LinkedInRequest
{
    use TraitOAuthIsSetUp;

    private const BEARER = 'Bearer ';
    public const HEADER_RESOURCE_ID = 'X-LinkedIn-Id';
    public Client $client;
    public string $token;

    public function __construct()
    {
        $this->client = $this->getSetUpClient();
        $this->token = $this->getSetUpToken();
    }

    /**
     * @throws CouldNotCreateACampaign
     */
    public function create(array $parameters)
    {
        $uri = $this->client->buildUrl(UrlEnums::URL['AD_CAMPAIGN'], $parameters);

        $header = ['Authorization' => self::BEARER . $this->token];

        try {
            $request = new LinkedInRequest();
            $response = $request->send('POST', $uri, $header, []);
        } catch (RequestException $e) {
            throw new CouldNotCreateACampaign($e->getMessage(), $e->getCode());
        }
        $externalId = $response->getHeaderLine(self::HEADER_RESOURCE_ID);

        if (empty($externalId)) {
            throw new CouldNotCreateACampaign('LinkedIn : Failed to create a campaign because of empty external id');
        }
        $campaign = new Campaign();
        $campaign->setExternalId((int) $externalId);

        return $campaign;
    }

    /**
     * @throws CouldNotDeleteACampaign
     */
    public function delete(int $campaignId): void
    {
        $parameters = json_encode([
            'patch' => [
                '$set' => [
                    'status' => 'ARCHIVED',]
            ]
        ]);
        $uri = $this->client->buildUrl(UrlEnums::URL['AD_CAMPAIGN'].'/'.$campaignId, $parameters);
        $header = ['Authorization' => self::BEARER. $this->token];
        try {
            (new LinkedInRequest())->send('POST', $uri, $header, []);
        } catch (RequestException $e) {
            throw new CouldNotDeleteACampaign($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @throws CouldNotUpdateACampaign
     */
    public function update(int $campaignId, array $params): void
    {
        $params['external_id'] = $campaignId;
        $parameters = json_encode([
            'patch' => [
                '$set' => $params,
            ],
        ]);
        $uri = $this->client->buildUrl(UrlEnums::URL['AD_CAMPAIGN'].'/'.$campaignId, $parameters);
        $header = ['Authorization' => 'Bearer ' . $this->token];
        try {
            (new LinkedInRequest())->send('POST', $uri, $header, []);
        } catch (RequestException $e) {
            throw new CouldNotUpdateACampaign($e->getMessage(), $e->getCode());
        }
    }
}


