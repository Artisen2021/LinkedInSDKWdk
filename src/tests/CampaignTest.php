<?php

namespace Artisen2021\LinkedInSDK\tests;

use Artisen2021\LinkedInSDK\Authentication\AccessToken;
use Artisen2021\LinkedInSDK\Authentication\Client;
use Artisen2021\LinkedInSDK\Distribution\Campaign;
use Artisen2021\LinkedInSDK\Distribution\CampaignGroup;
use Artisen2021\LinkedInSDK\Http\AccessTokenRequest;
use Artisen2021\LinkedInSDK\Http\CampaignGroupRequest;
use Artisen2021\LinkedInSDK\Http\CampaignRequest;
use PHPUnit\Framework\TestCase;
use Mockery;
use Faker;

class CampaignTest extends TestCase
{
    use TraitClientTest;

    public Client $client;

    public array $campaignData = [
        'account' => 'urn:li:sponsoredAccount:' . '123456789',
        'campaignGroup' => 'urn:li:sponsoredCampaignGroup:' . '112233445',
        'costType' => 'CPM',
        'locale' => [
            'country' => 'NL',
            'language' => 'nl',
        ],
        'name' => 'Test Campaign',
        'runSchedule' => [
            'start' => '2020-01-01',
            'end' => '2020-12-31',
        ],
        'type' => 'SPONSORED_UPDATES',
        'optimizationTargetType' => 'MAX_REACH',//$this->getOptimizationTargetType($params['objective']),
        'totalBudget' => [
            'amount' => '100',
            'currencyCode' => 'USD',
        ],
        'dailyBudget' => [
            'amount' => '10',
            'currencyCode' => 'USD',
        ],
        'objectiveType' => 'BRAND_AWARENESS',
        'unitCost' => [
            'amount' => '0',
            'currencyCode' => 'USD',
        ],
        'targetingCriteria' => [
            'include' => [
                'and' => [
                    [
                        'or' => [
                            'urn:li:adTargetingFacet:locations' => 'Netherlands',
                        ],
                    ],
                    [
                        'or' => [
                            'urn:li:adTargetingFacet:titles' => ['Car Repairer', 'Car Fixer', 'Car Engineer'],
                            'urn:li:adTargetingFacet:skills' => ['Education' => 'Education',
                                'Engineering' => 'Engineering',
                                'Finance' => 'Finance',
                                'Government' => 'Government',
                                'Healthcare' => 'Healthcare'],
                        ],
                    ],
                ],
            ],
        ],
        'format' => 'SINGLE_VIDEO',//EloquentAd::FORMATS[$params['campaign_ad_type']],
        'status' => 'ACTIVE'//config('services.ad_delivery') ? self::ACTIVE_STATUS : self::DRAFT_STATUS,
    ];

    public function mockClientAccessTokenAndCampaignGroup()
    {
        $this->client = $this->getClient();
        $faker = Faker\Factory::create();
        $fakerCode = $faker->password;
        $fakerToken = $faker->password;

        $mockAccessTokenRequest = Mockery::mock(AccessTokenRequest::class);
        $mockAccessTokenRequest->shouldReceive('getAccessToken');

        $mockAccessToken = Mockery::mock(AccessToken::class);
        $mockAccessToken->shouldReceive('getToken')->andReturn($fakerToken);

        $mockAccessTokenRequest->getAccessToken($fakerCode);

        $campaignGroupRequest = Mockery::mock(CampaignGroupRequest::class);
        $campaignGroupRequest->shouldReceive('create')->andReturn(CampaignGroup::class);

        $campaignGroup = Mockery::mock(CampaignGroup::class);
        $campaignGroup->shouldReceive('getExternalId')->andReturn(112233445);

    }

    public function testCampaignIsCreated()
    {
        $this->mockClientAccessTokenAndCampaignGroup();

        $campaignRequest = Mockery::mock(CampaignRequest::class);
        $campaignRequest->shouldReceive('create')->andReturn(Campaign::class);

        $campaign = $campaignRequest->create($this->campaignData);
        $this->assertEquals(Campaign::class, $campaign);
    }

    public function testCampaignIdIsRetrieved()
    {
        $this->mockClientAccessTokenAndCampaignGroup();

        $campaignRequest = Mockery::mock(CampaignRequest::class);
        $campaignRequest->shouldReceive('create');

        $campaign = Mockery::mock(Campaign::class);
        $campaign->shouldReceive('getExternalId')->andReturn(111222333);

        $campaignRequest->create($this->campaignData);
        $campaignResult = $campaign->getExternalId();

        $this->assertEquals(111222333, $campaignResult);
    }

    public function testCampaignIsDeleted()
    {
        $this->mockClientAccessTokenAndCampaignGroup();

        $campaignRequest = Mockery::mock(CampaignRequest::class);
        $campaignRequest->shouldReceive('create');

        $campaign = Mockery::mock(Campaign::class);
        $campaign->shouldReceive('getExternalId')->andReturn(111222333);

        $campaignRequest->shouldReceive('delete');

        $campaign = Mockery::mock(Campaign::class);
        $campaign->shouldReceive('getExternalId')->andReturn();

        $campaignRequest->create($this->campaignData);
        $campaignRequest->delete(111222333);
        $campaignIdResult = $campaign->getExternalId();

        $this->assertEquals(null, $campaignIdResult);
    }

    public function testCampaignIsUpdated()
    {
        $this->mockClientAccessTokenAndCampaignGroup();

        $campaignRequest = Mockery::mock(CampaignRequest::class);
        $campaignRequest->shouldReceive('create');

        $campaign = Mockery::mock(Campaign::class);
        $campaign->shouldReceive('getExternalId')->andReturn(111222333);

        $campaignRequest->shouldReceive('update');

        $campaign->shouldReceive('getCountry')->andReturn('US');
        $campaign->shouldReceive('getName')->andReturn('Name changed');

        $campaignRequest->create($this->campaignData);
        $campaignRequest->update(111222333, [
            'name' => 'Name changed',
            'country' => 'US'
        ]);
        $campaignCountryResult = $campaign->getCountry();
        $campaignNameResult = $campaign->getName();

        $this->assertEquals('US', $campaignCountryResult);
        $this->assertEquals('Name changed', $campaignNameResult);
    }
}

