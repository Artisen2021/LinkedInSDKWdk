<?php
declare(strict_types=1);

namespace Artisen2021\LinkedInSDK\Http;

use Artisen2021\LinkedInSDK\Authentication\AccessToken;
use Artisen2021\LinkedInSDK\Authentication\Client;
use Artisen2021\LinkedInSDK\Exception\CouldNotGetAccessToken;
use GuzzleHttp\Exception\RequestException;

class AccessTokenRequest
{
    public Client $client;
    public AccessToken $accessToken;
    public AccessTokenRequest $requestBody;
    protected const OAUTH2_GRANT_TYPE = 'authorization_code';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getAccessToken(string $code): AccessToken
    {
        if (!empty($code)) {
            $parameters = [
                'grant_type' => self::OAUTH2_GRANT_TYPE,
                'code' => $code,
                'redirect_uri' => $this->client->getRedirectUrl(),
                'client_id' => $this->client->getClientId(),
                'client_secret' => $this->client->getClientSecret(),
            ];
            $uri = $this->client->buildUrl('accessToken', $parameters);

            $header = ['Content-Type' => 'x-www-form-urlencoded'];

            try {
                $request = new LinkedInRequest();
                $response = $request->send('POST', $uri, $header, []);
            } catch (RequestException $e) {
                throw new CouldNotGetAccessToken($e->getMessage(), $e->getCode());
            }
            $this->accessToken = new AccessToken();
            $this->requestBody = $this->setAccessToken(AccessToken::fromResponse($response));
            $this->accessToken->setToken($this->requestBody->accessToken->token);
            $this->accessToken->setTokenExpirationTime($this->requestBody->accessToken->tokenExpirationTime);
        }
        return $this->accessToken;
    }

    public function setAccessToken($accessToken): AccessTokenRequest
    {
        if ($accessToken instanceof AccessToken) {
            $this->accessToken = $accessToken;
        } else {
            throw new \InvalidArgumentException('$accessToken must be instance of \LinkedIn\AccessToken class');
        }
        return $this;
    }
}