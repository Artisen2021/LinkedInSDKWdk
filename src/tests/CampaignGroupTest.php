<?php

namespace Artisen2021\LinkedInSDK\tests;

use Artisen2021\LinkedInSDK\Authentication\AccessToken;
use Artisen2021\LinkedInSDK\Authentication\Client;
use Artisen2021\LinkedInSDK\Distribution\CampaignGroup;
use Artisen2021\LinkedInSDK\Http\AccessTokenRequest;
use Artisen2021\LinkedInSDK\Http\CampaignGroupRequest;
use Artisen2021\LinkedInSDK\Http\LinkedInRequest;
use Artisen2021\LinkedInSDK\Http\TraitOAuthIsSetUp;
use Artisen2021\LinkedInSDK\UrlEnums;
use GuzzleHttp\ClientTrait;
use Mockery;
use PHPUnit\Framework\TestCase;
use Faker;

class CampaignGroupTest extends TestCase
{
    use TraitClientTest;

    public Client $client;

    public function mockClientAndAccessToken()
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
    }

    public function testCampaignGroupIsCreated()
    {
        $this->mockClientAndAccessToken();

        $campaignGroupRequest = Mockery::mock(CampaignGroupRequest::class);
        $campaignGroupRequest->shouldReceive('create')->andReturn(CampaignGroup::class);

        $campaignGroup = $campaignGroupRequest->create([
            'account' => 'urn:li:sponsoredAccount:' . '123456789',
            'name' => 'Test Campaign Group',
            'runSchedule' => [
                'start' => '2020-01-01',
                'end' => '2020-12-31',
            ],
            'status' => 'ACTIVE',
            'totalBudget' => [
                'amount' => '100',
                'currencyCode' => 'USD'
            ]
        ]);

        $this->assertEquals(CampaignGroup::class, $campaignGroup);
    }

    public function testCampaignGroupIdIsRetrieved()
    {
        $this->mockClientAndAccessToken();

        $campaignGroupRequest = Mockery::mock(CampaignGroupRequest::class);
        $campaignGroupRequest->shouldReceive('create');

        $campaignGroup = Mockery::mock(CampaignGroup::class);
        $campaignGroup->shouldReceive('getExternalId')->andReturn(112233445);

        $campaignGroupRequest->create([
            'account' => 'urn:li:sponsoredAccount:' . '123456789',
            'name' => 'Test Campaign Group',
            'runSchedule' => [
                'start' => '2020-01-01',
                'end' => '2020-12-31',
            ],
            'status' => 'ACTIVE',
            'totalBudget' => [
                'amount' => '100',
                'currencyCode' => 'USD'
            ]
        ]);
        $campaignGroupResult = $campaignGroup->getExternalId();

        $this->assertEquals(112233445, $campaignGroupResult);
    }

    public function testCampaignGroupIsDeleted()
    {
        $this->mockClientAndAccessToken();

        $campaignGroupRequest = Mockery::mock(CampaignGroupRequest::class);
        $campaignGroupRequest->shouldReceive('create');

        $campaignGroup = Mockery::mock(CampaignGroup::class);
        $campaignGroup->shouldReceive('getExternalId')->andReturn(112233445);

        $campaignGroupRequest->shouldReceive('delete');

        $campaignGroup = Mockery::mock(CampaignGroup::class);
        $campaignGroup->shouldReceive('getExternalId')->andReturn();

        $campaignGroupRequest->create([
            'account' => 'urn:li:sponsoredAccount:' . '123456789',
            'name' => 'Test Campaign Group',
            'runSchedule' => [
                'start' => '2020-01-01',
                'end' => '2020-12-31',
            ],
            'status' => 'ACTIVE',
            'totalBudget' => [
                'amount' => '100',
                'currencyCode' => 'USD'
            ]
        ]);
        $campaignGroupRequest->delete(112233445);
        $campaignGroupIdResult = $campaignGroup->getExternalId();

        $this->assertEquals(null, $campaignGroupIdResult);
    }

    public function testCampaignGroupIsUpdated()
    {
        $this->mockClientAndAccessToken();

        $campaignGroupRequest = Mockery::mock(CampaignGroupRequest::class);
        $campaignGroupRequest->shouldReceive('create');

        $campaignGroup = Mockery::mock(CampaignGroup::class);
        $campaignGroup->shouldReceive('getExternalId')->andReturn(112233445);

        $campaignGroupRequest->shouldReceive('update');

        $campaignGroup->shouldReceive('getStatus')->andReturn('PAUSED');
        $campaignGroup->shouldReceive('getName')->andReturn('Name changed');

        $campaignGroupRequest->create([
            'account' => 'urn:li:sponsoredAccount:' . '123456789',
            'name' => 'Test Campaign Group',
            'runSchedule' => [
                'start' => '2020-01-01',
                'end' => '2020-12-31',
            ],
            'status' => 'ACTIVE',
            'totalBudget' => [
                'amount' => '100',
                'currencyCode' => 'USD'
            ]
        ]);
        $campaignGroupRequest->update(112233445, [
            'name' => 'Name changed',
            'status' => 'PAUSED'
        ]);
        $campaignGroupStatusResult = $campaignGroup->getStatus();
        $campaignGroupNameResult = $campaignGroup->getName();

        $this->assertEquals('PAUSED', $campaignGroupStatusResult);
        $this->assertEquals('Name changed', $campaignGroupNameResult);
    }
}