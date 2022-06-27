<?php

use Artisen2021\LinkedInSDK\Authentication\AccessToken;
use Artisen2021\LinkedInSDK\Authentication\Client;
use Artisen2021\LinkedInSDK\Distribution\Campaign;
use Artisen2021\LinkedInSDK\Distribution\CampaignGroup;
use Artisen2021\LinkedInSDK\Distribution\ImageAd;
use Artisen2021\LinkedInSDK\Http\AccessTokenRequest;
use Artisen2021\LinkedInSDK\Http\AdRequest;
use Artisen2021\LinkedInSDK\Http\CampaignGroupRequest;
use Artisen2021\LinkedInSDK\Http\CampaignRequest;
use Artisen2021\LinkedInSDK\Http\ImageAdRequest;
use Artisen2021\LinkedInSDK\tests\TraitClientTest;
use PHPUnit\Framework\TestCase;

class AdTest extends TestCase
{
    use TraitClientTest;

    public Client $client;

    public function mockClientAccessTokenCampaignGroupAndCampaign()
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
    }

    public function testCreateImageAdIfMediaTypeIsEqualToImage()
    {
        $this->mockClientAccessTokenCampaignGroupAndCampaign();

        $adRequest = Mockery::mock(AdRequest::class);
        $adRequest->shouldReceive('create')->andReturn(ImageAd::class);

        $parameters = [
            'media_type' => 'image',
            'external_campaign_id' => '123',
            'external_account_id' => '456',
            'external_id' => '789',
        ];
        $ad = $adRequest->create($parameters);
        $this->assertEquals(ImageAd::class, $ad);
    }

    public function testAdIsDeleted()
    {
        $this->mockClientAccessTokenCampaignGroupAndCampaign();

        $adRequest = Mockery::mock(AdRequest::class);
        $adRequest->shouldReceive('create')->andReturn(ImageAd::class);

        $adImageRequest = Mockery::mock(ImageAdRequest::class);
        $adImageRequest->shouldReceive('create')->andReturn(ImageAd::class);

        $adImage = Mockery::mock(ImageAd::class);
        $adImage->shouldReceive('getExternalId')->andReturn(111112222);

        $adRequest->shouldReceive('delete');

        $adImage = Mockery::mock(ImageAd::class);
        $adImage->shouldReceive('getExternalId')->andReturn();

        $parameters = [
            'media_type' => 'image',
            'external_campaign_id' => '123',
            'external_account_id' => '456',
            'external_id' => '789',
        ];
        $adImageRequest->create($parameters);
        $adRequest->delete(111112222);
        $adIdResult = $adImage->getExternalId();

        $this->assertEquals(null, $adIdResult);
    }
}