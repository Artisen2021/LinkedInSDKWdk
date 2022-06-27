<?php

namespace Artisen2021\LinkedInSDK\tests;

use PHPUnit\Framework\TestCase;
use Artisen2021\LinkedInSDK\Authentication\Client;

class ClientTest extends TestCase
{
    use TraitClientTest;

    public Client $client;

    public function testAClientIsCreated()
    {
        $this->client = $this->getClient();
        $clientId = $this->client->getClientId();
        $clientSecret = $this->client->getClientSecret();;

        $this->assertEquals($clientId,getenv('LINKEDIN_CLIENT_ID'));
        $this->assertEquals($clientSecret,getenv('LINKEDIN_CLIENT_SECRET'));
        $this->assertTrue($this->client instanceof Client);
    }

    public function testRedirectUrlIsRetrieved()
    {
        $this->client = $this->getClient();
        $_SERVER['HTTP_HOST'] = 'oauth.pstmn.io/v1/callback';
        $_SERVER['HTTPS'] = 'https';
        $redirectUrl = $this->client->getRedirectUrl();
        $this->assertEquals($redirectUrl,'https://oauth.pstmn.io/v1/callback/');
    }

    public function testLoginUrlIsRetrieved()
    {
        $this->client = $this->getClient();

        $loginUrl = $this->client->getLoginUrl();

        $this->assertEquals($loginUrl,'https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id='.$this->client->getClientId().'&redirect_uri='.$this->client->getRedirectUrl().'&state='.$this->client->getState().'&scope=r_liteprofile%20r_emailaddress');
    }
}