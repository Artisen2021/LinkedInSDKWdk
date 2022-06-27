<?php

namespace Artisen2021\LinkedInSDK\tests;

use Artisen2021\LinkedInSDK\Authentication\AccessToken;
use Artisen2021\LinkedInSDK\Authentication\Client;
use Artisen2021\LinkedInSDK\Distribution\CampaignGroup;
use Artisen2021\LinkedInSDK\Distribution\ImageAd;
use Artisen2021\LinkedInSDK\Distribution\VideoAd;
use Artisen2021\LinkedInSDK\Http\AccessTokenRequest;
use Artisen2021\LinkedInSDK\Http\AdRequest;
use Artisen2021\LinkedInSDK\Http\CampaignGroupRequest;
use Artisen2021\LinkedInSDK\Http\CampaignRequest;
use Mockery;
use Faker;
use PHPUnit\Framework\TestCase;

class VideoAdTest extends TestCase
{
    use TraitClientTest;

    public Client $client;

    public function mockClientAccessTokenCampaignGroupCampaignAndAdRequest()
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

        $campaignRequest = Mockery::mock(CampaignRequest::class);
        $campaignRequest->shouldReceive('create');

        $adRequest = Mockery::mock(AdRequest::class);
        $adRequest->shouldReceive('create')->andReturn(VideoAd::class);
    }

    public function testVideoAdIsCreated()
    {
        $this->mockClientAccessTokenCampaignGroupCampaignAndAdRequest();

        $videoAdRequest = Mockery::mock(AdRequest::class);
        $videoAdRequest->shouldReceive('create')->andReturn(VideoAd::class);

        $parameters = [
            'account_id' => '123456789',
            'page_id' => '2',
            'campaign_id' => '112233445',
            'type' => 'image',
            'text' => 'Search a php developer',
            'title' => 'Job offer',
            'landing_page_url' => 'https://www.example.com/image.jpg',
            'media_url' => 'https://www.example.com/image.jpg',
            'call_to_action' => 'action',
        ];

        $videoAd = $videoAdRequest->create($parameters);
        $this->assertEquals($videoAd, VideoAd::class);
    }
}
