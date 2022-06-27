<?php

namespace Artisen2021\LinkedInSDK\tests;

use Artisen2021\LinkedInSDK\Authentication\Client;

trait TraitClientTest
{
    public Client $client;

    public function getClient(): Client
    {
        return new Client(
            getenv('LINKEDIN_CLIENT_ID'),
            getenv('LINKEDIN_CLIENT_SECRET')
        );
    }

}