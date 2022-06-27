<?php
declare(strict_types=1);

namespace Artisen2021\LinkedInSDK\Http;

use Artisen2021\LinkedInSDK\Authentication\AccessToken;
use Exception;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Artisen2021\LinkedInSDK\Authentication\Client;
use Psr\Http\Message\ResponseInterface;


class LinkedInRequest
{
    protected array $bodyParams = [];
    protected array $requestHeaders = [];
    protected array $responseHeaders = [];
    protected int $responseHttpStatusCode;

    //TODO: Commented out code?
//    public Client $client;
//    public AccessToken $accessToken;
//    public const HEADER_RESOURCE_ID = 'X-LinkedIn-Id';
//
//    public function __construct(
//        Client $client,
//        AccessToken $accessToken)
//    {
//        $this->client = $client;
//        $this->accessToken = $accessToken;
//    }

    /**
     * @throws Exception
     */
    public function send($method, $endpoint, $headers=[], $parameters=[]): ResponseInterface
    {
        if ($parameters) {
            $this->bodyParams = ['form_params' => $parameters];
        }

        if ($headers) {
            foreach ($headers as $k => $v) {
                $this->requestHeaders = [$k => $v];
            }
        }

        $request = new Request($method, $endpoint, $this->requestHeaders, json_encode($this->bodyParams));
        try {
            $response = (new GuzzleClient())->send($request);
        } catch (GuzzleException $e) {
            //we'll need to throw dedicated exception here
            throw new Exception($e->getMessage(), $e->getCode());
        }

        $this->responseHttpStatusCode = $response->getStatusCode();
        $this->responseHeaders = $response->getHeaders();

        return $response;
    }
}
