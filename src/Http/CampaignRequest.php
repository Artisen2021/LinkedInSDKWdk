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
use Artisen2021\LinkedInSDK\Builder\CampaignBuilder;

class CampaignRequest extends LinkedInRequest
{
    private const BEARER = 'Bearer ';
    public const HEADER_RESOURCE_ID = 'X-LinkedIn-Id';
    public Client $client;
    public string $token;
    private CampaignBuilder $builder;


    public function __construct(Client $client, string $token)
    {
        $this->client = $client;
        $this->token = $token;
        $this->builder = new CampaignBuilder();
    }

    /**
     * @throws CouldNotCreateACampaign
     */
    public function create(array $parameters)
    {
        $requestBody = $this->builder->create($parameters);

        $uri = $this->client->buildUrl(UrlEnums::URL['AD_CAMPAIGNS'], []);

        $header = [
            'Authorization' => self::BEARER . $this->token,
            'Content-type' => 'application/json'
        ];

        try {
            $request = new LinkedInRequest();
            $response = $request->send('POST', $uri, $header, $requestBody);
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
        $requestBody = $this->builder->delete();

        $uri = $this->client->buildUrl(UrlEnums::URL['AD_CAMPAIGNS'].'/'.$campaignId, []);
        $header = ['Authorization' => self::BEARER. $this->token];

        try {
            (new LinkedInRequest())->send('POST', $uri, $header, $requestBody);
        } catch (RequestException $e) {
            throw new CouldNotDeleteACampaign($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @throws CouldNotUpdateACampaign
     */
    public function update(int $campaignId, array $params): void
    {
        $requestBody = $this->builder->update($params);

        $uri = $this->client->buildUrl(UrlEnums::URL['AD_CAMPAIGNS'].'/'.$campaignId, []);
        $header = ['Authorization' => 'Bearer ' . $this->token];
        try {
            (new LinkedInRequest())->send('POST', $uri, $header, $requestBody);
        } catch (RequestException $e) {
            throw new CouldNotUpdateACampaign($e->getMessage(), $e->getCode());
        }
    }
}


