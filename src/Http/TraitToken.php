<?php

namespace Artisen2021\LinkedInSDK\Http;

trait TraitToken
{
    public function getTokenCode()
    {
        return file_exists('token.json')
            ? json_decode(file_get_contents('token.json'), true)['token']
            : '';
    }
}
