<?php

namespace Artisen2021\LinkedInSDK\tests;

use Artisen2021\LinkedInSDK\Authentication\Client;
use Artisen2021\LinkedInSDK\Http\OAuthCodeRequest;
use PHPUnit\Framework\TestCase;
use Faker;
use \Mockery;

class OAuthTest extends TestCase
{
    use TraitClientTest;

    public OAuthCodeRequest $oAuthCodeRequest;
    public Client $client;

    public function testOAuthCodeIsRetrieved()
    {
        $this->client = $this->getClient();
        $this->oAuthCodeRequest = new OAuthCodeRequest($this->client);
        $response = 'https://oauth.pstmn.io/v1/callback/?code=1234';
        $oAuthCode = $this->oAuthCodeRequest->getCode($response);
        $this->assertEquals('1234', $oAuthCode);
    }

    public function testOAuthCodeRequestIsSentAndOAuthCodeIsRetrieved()
    {
        $faker = Faker\Factory::create();
        $fakerCode = $faker->password;
        $this->client = $this->getClient();

        $mockOAuthCode = Mockery::mock(OAuthCodeRequest::class);
        $mockOAuthCode->shouldReceive('getOAuthCode')->shouldReceive('getCode')->andReturn($fakerCode);
        $mockResult = $mockOAuthCode->getOAuthCode();
        $mockResult = $mockOAuthCode->getCode($mockResult);
        $this->assertEquals($mockResult,$fakerCode);
    }
}