<?php
declare(strict_types=1);


namespace Artisen2021\LinkedInSDK\Authentication;

use Artisen2021\LinkedInSDK\Exception\CouldNotGetAccessToken;
use Exception;

class AccessToken
{
    public string $token;
    public int $tokenExpirationTime;

    public function getToken(): string
    {
        return $this->token;
    }

    public function getTokenExpirationTime(): int
    {
        return $this->tokenExpirationTime;
    }

    public function setToken($token): void
    {
        $this->token = $token;
    }

    public function setTokenExpirationTime($expiresIn): void
    {
        $this->tokenExpirationTime = $expiresIn;
    }

    public static function fromResponse($response): AccessToken
    {
        return static::fromResponseArray(
            Client::responseToArray($response)
        );
    }

    public static function fromResponseArray($responseArray): AccessToken
    {
        if (!is_array($responseArray)) {
            throw new \InvalidArgumentException(
                'Argument is not an array'
            );
        }
        if (!isset($responseArray['access_token'])) {
            throw new \InvalidArgumentException(
                'Access token is not available'
            );
        }
        if (!isset($responseArray['expires_in'])) {
            throw new \InvalidArgumentException(
                'Access token expiration date is not specified'
            );
        }
        return new static(
            $responseArray['access_token'],
            $responseArray['expires_in']
        );
    }
}