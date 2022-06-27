<?php

namespace Artisen2021\LinkedInSDK\tests;

use Artisen2021\LinkedInSDK\Authentication\AccessToken;
use Artisen2021\LinkedInSDK\Authentication\Client;
use Artisen2021\LinkedInSDK\Http\AccessTokenRequest;
use PHPUnit\Framework\TestCase;
use Faker;
use \Mockery;

class AccessTokenTest extends TestCase
{
    use TraitClientTest;

    public Client $client;

    public function testAccessTokenIsRetrieved()
    {
        $faker = Faker\Factory::create();
        $fakerToken = $faker->password;
        $fakerCode = $faker->password;
        $this->client = $this->getClient();

        $mockAccessTokenRequest = Mockery::mock(AccessTokenRequest::class);
        $mockAccessTokenRequest->shouldReceive('getAccessToken');

        $mockAccessToken = Mockery::mock(AccessToken::class);
        $mockAccessToken->shouldReceive('getToken')->andReturn($fakerToken);

        $mockAccessTokenRequest->getAccessToken($fakerCode);
        $mockResult = $mockAccessToken->getToken();

        $this->assertEquals($mockResult,$fakerToken);

        return $mockResult;
    }
}