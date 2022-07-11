<?php
declare(strict_types=1);

namespace Artisen2021\LinkedInSDK\Http;

use Artisen2021\LinkedInSDK\Authentication\AccessToken;
use Artisen2021\LinkedInSDK\Authentication\Client;
use Artisen2021\LinkedInSDK\Exception\CouldNotFetchLocation;
use Artisen2021\LinkedInSDK\Exception\CouldNotFetchSimilar;
use Artisen2021\LinkedInSDK\Exception\CouldNotFetchUrns;
use Artisen2021\LinkedInSDK\UrlEnums;
use GuzzleHttp\Exception\RequestException;

class TargetingRequest extends LinkedInRequest
{
    use TraitToken;

    private const BEARER = 'Bearer ';
    public Client $client;
    public string $token;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->token = $this->getTokenCode();
    }

    //https://docs.microsoft.com/en-us/linkedin/marketing/integrations/ads/advertising-targeting/ads-targeting?view=li-lms-2022-06&tabs=http#ad-targeting-entities

    /**
     * @throws CouldNotFetchLocation
     */
    public function fetchLocation(string $locationName): string
    {
        $queryString =
            'q=TYPEAHEAD' .
            '&facet=urn:li:adTargetingFacet:locations' .
            '&query=' . $locationName;

        $uri = rtrim($this->client->buildUrl(UrlEnums::URL['AD_TARGETING_ENTITIES']. '?' . $queryString, []),'?');

        $header = ['Authorization' => self::BEARER . $this->token];

        try {
            $request = new LinkedInRequest();
            $response = $request->send('GET', $uri, $header,[]);
            $body = json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            throw new CouldNotFetchLocation($e->getMessage(), $e->getCode());
        }
        return empty($body['elements']) ? '' : $body['elements'][0]['urn'];
    }

    /**
     * @throws CouldNotFetchUrns
     */
    public function fetchUrns(string $query, string $facet, $exact = false): array
    {
        $urns = [];
        $queryString =
            'q=TYPEAHEAD' .
            '&facet=urn:li:adTargetingFacet:' . $facet .
            '&query=' . $query;

        $uri = $this->client->buildUrl(UrlEnums::URL['AD_TARGETING_ENTITIES']. '?' . $queryString, []);

        $header = ['Authorization' => self::BEARER . $this->token];

        //TODO: For predictability,don't bother using if/else, just stick to several if statements. Will help with mutation testing :)
        try {
            $request = new LinkedInRequest();
            $response = $request->send('GET', $uri, $header, []);
            $body = json_decode($response->getBody()->getContents(), true);
            if ($exact && !empty($body['elements'])) {
                $urns[] = $body['elements'][0]['urn'];
            } else {
                foreach ($body['elements'] as $element) {
                    $urns[] = $element['urn'];
                }
            }
        } catch (RequestException $e) {
            throw new CouldNotFetchUrns($e->getMessage(), $e->getCode());
        }
        return $urns;
    }

    /**
     * @throws CouldNotFetchSimilar
     */
    public function fetchSimilar(string $urn, string $facet): array
    {
        $titles = [];
        $queryString =
            'q=similarEntities' .
            '&facet=urn:li:adTargetingFacet:' . $facet .
            '&entities=' . $urn;

        $uri = $this->client->buildUrl(UrlEnums::URL['AD_TARGETING_ENTITIES']. '?' . $queryString, []);

        $header = ['Authorization' => 'Bearer ' . $this->token];

        try {
            $request = new LinkedInRequest();
            $response = $request->send('GET', $uri, $header, []);
            $body = json_decode($response->getBody()->getContents(), true);
            foreach ($body['elements'] as $element) {
                $titles[] = $element['urn'];
            }
        } catch (RequestException $e) {
            throw new CouldNotFetchSimilar($e->getMessage(), $e->getCode());
        }
        return $titles;
    }


}
