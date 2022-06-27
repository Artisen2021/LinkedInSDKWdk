<?php
declare(strict_types=1);

namespace Artisen2021\LinkedInSDK\Http;

use Artisen2021\LinkedInSDK\Authentication\AccessToken;
use Artisen2021\LinkedInSDK\Authentication\Client;
use Artisen2021\LinkedInSDK\tests\TraitClientTest;

//For future implementation of other traits, may be good to have seperate traits folder/namespace
trait TraitOAuthIsSetUp
{
    use TraitClientTest;

    public Client $client;
    public AccessToken $accessToken;
    public string $token;

    public function getSetUpClient(): Client
    {
        $this->client = $this->getClient();
        return $this->client;
    }

    public function getSetUpToken(): string
    {
        $this->token = $this->accessToken->getToken();
        return $this->token;
    }

}